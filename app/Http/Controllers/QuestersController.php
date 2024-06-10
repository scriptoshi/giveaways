<?php

namespace App\Http\Controllers;

use App\Enums\QuesterStatus;
use App\Enums\QuestStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Quester as QuesterResource;
use App\Models\Coin;
use App\Models\Quester;
use Inertia\Inertia;

use App\Models\Giveaway;
use App\Models\User;
use App\Web3\Prize;
use App\Web3\Utils as Web3Utils;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Str;
use SWeb3\Utils;

class QuestersController extends Controller
{


    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function sleep(Request $request)
    {
        $query = Quester::query()
            ->where('user_id', $request->user()->id)
            ->whereNotNull('completed_at')
            ->with([
                'user.account',
                'giveaway.project.logo'
            ]);
        $questers = $query->clone()->latest()->take(100)->get();
        $signed = $query->whereNull('sleep_hash')
            ->whereNotNull('sleep_signature')
            ->latest()
            ->take(100)
            ->get()
            ->unique('sleep_signature');
        return Inertia::render('Questers/Sleep', [
            'total' => fn () => Quester::query()
                ->where('user_id', $request->user()->id)
                ->sum('sleep'),
            'available' => fn () => Quester::query()
                ->where('user_id', $request->user()->id)
                ->whereNull('sleep_signature')
                ->whereNotNull('completed_at')
                ->whereNotNull('sleep')
                ->where('sleep', '>=', 0)
                ->sum('sleep'),
            'questers' => fn () =>  QuesterResource::collection($questers),
            'signed' => fn () =>  QuesterResource::collection($signed),
            'prizeClaim' => json_decode(\File::get(resource_path('js/abi/SleepClaim.json')), true)

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function pump(Request $request, Quester $quester)
    {
        $this->authorize('update', $quester);
        if ($quester->sleep_signature) {
            throw ValidationException::withMessages(['code' => 'You already claimed your sleep tokens']);
        }
        if ($quester->last_pump_at > now()->subHour()) {
            throw ValidationException::withMessages(['code' => __('You next pump is in :minutes minutes', ['minutes' => $quester->last_pump_at->addHour()->diffInMinutes(now())])]);
        }
        if (!$quester->completed_at) {
            throw ValidationException::withMessages(['code' => 'Complete all tasks before pumping!']);
        }
        $giveaway =  $quester->giveaway;
        if ($giveaway->sleep_balance < 100)
            throw ValidationException::withMessages(['code' => 'Giveaway sleep faucet is dry']);
        $giveaway->sleep_balance -= 100;
        $giveaway->save();
        $quester->pump += 1;
        $quester->sleep += 100;
        $quester->last_pump_at = now();
        $quester->save();
        $quester->pumps()->create([
            'user_id' => $request->user()->id
        ]);
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function claim(Request $request, Quester $quester)
    {
        $this->authorize('update', $quester);
        if (!$quester->completed_at) {
            return ['error' => 'Complete all tasks before claiming!'];
        }
        if ($quester->status == QuesterStatus::CLAIMED) {
            return ['error' => 'You already claimed this prize'];
        }
        if ($quester->status != QuesterStatus::WINNER) {
            return ['error' => 'You did not win!'];
        }

        if ($quester->signature) {
            return $quester;
        }
        $address = Utils::toChecksumAddress($request->address);
        if (!$request->user()->accounts()->where('address', $address)->exists())
            return ['error' => 'Invalid or unregistered address!'];
        $quester->load("giveaway");
        $prizeClaim = json_decode(\File::get(resource_path('js/abi/PrizeClaim.json')), true);
        $contract =  $prizeClaim['addresses'][$quester->giveaway->chainId];
        $coin = Coin::query()
            ->where('chainId', $quester->giveaway->chainId)
            ->where('symbol', $quester->giveaway->symbol)
            ->first();
        $wei = Web3Utils::toSatoshi($quester->giveaway->prize, $coin->decimals);
        $uuid = Str::uuid();
        $prizeInfo = [
            'to' =>  $address,
            'amount' => $wei,
            'uuid' => $uuid->getInteger()->toString(),
            'name' => "PrizeClaim", // embedded in contract
            'contract' => $contract,
            'chainId' => $quester->giveaway->chainId,
            'version' => "1",
            'pvk' => config('app.pvk'),
        ];
        $prize = new Prize($prizeInfo);
        $quester->uuid = $uuid->toString();
        $quester->claim = [
            'to' =>  $address,
            'amount' => $wei,
            'uuid' => $uuid->getInteger()
        ];
        $quester->signature = $prize->getSignature();
        $quester->save();
        return  $quester;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function claimSleep(Request $request)
    {
        $query = Quester::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('sleep_signature')
            ->whereNotNull('sleep')
            ->where('sleep', '>=', 0)
            ->whereNotNull('completed_at');
        $quester =  $query->clone()->with(['giveaway'])->first();
        $total =  $query->clone()->sum('sleep');
        if ($total == 0) return ['error' => 'Nothing to claim'];
        $address = Utils::toChecksumAddress($request->address);
        if (!$request->user()->accounts()->where('address', $address)->exists())
            return ['error' => 'Invalid or unregistered address!'];
        $prizeClaim = json_decode(\File::get(resource_path('js/abi/SleepClaim.json')), true);
        $contract =  $prizeClaim['addresses'][$quester->giveaway->chainId];
        $wei = Web3Utils::toSatoshi($total, 18);
        $uuid = Str::uuid();
        $prizeInfo = [
            'to' =>  $address,
            'amount' => $wei,
            'uuid' => $uuid->getInteger()->toString(),
            'name' => "PrizeClaim", // embedded in contract
            'contract' => $contract,
            'chainId' => $quester->giveaway->chainId,
            'version' => "1",
            'pvk' => config('app.pvk'),
        ];
        $prize = new Prize($prizeInfo);
        $signature = $prize->getSignature();
        $query->clone()->update([
            'sleep_claim' => [
                'to' =>  $address,
                'amount' => $wei,
                'uuid' => $uuid->getInteger()
            ],
            'sleep_signature' => $signature
        ]);
        $quester = Quester::query()
            ->where('sleep_signature', $signature)
            ->first();
        return  $quester;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function claimedSleep(Request $request, Quester $quester)
    {
        $this->authorize('update', $quester);
        Quester::query()
            ->where('user_id', $request->user()->id)
            ->where('sleep_signature', $quester->sleep_signature)
            ->update([
                'sleep_hash' => $request->txhash,
                'sleep_claimed_at' => now()
            ]);
        return  back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function claimed(Request $request, Quester $quester)
    {
        $this->authorize('update', $quester);
        if (!$quester->completed_at) {
            return ['error' => 'Complete all tasks before pumping!'];
        }
        $quester->status = QuesterStatus::CLAIMED;
        $quester->comment = $request->txhash;
        $quester->save();
        $quester->load('giveaway');
        $quester->giveaway->increment('num_claimed');
        return  back();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function boost(Request $request, Quester $quester)
    {
        $this->authorize('update', $quester);
        if (!$quester->completed_at) {
            throw ValidationException::withMessages(['boostId' => 'Complete all tasks first']);
        }
        $request->validate(['boostId' => ['required', 'string', 'exists:questers,qid']]);
        if ($quester->qid === $request->boostId) {
            throw ValidationException::withMessages(['boostId' => 'Cannot boost your own claim']);
        }
        $boost = Quester::query()
            ->where('qid', $request->boostId)
            ->first();
        if ($boost->giveaway_id != $quester->giveaway_id) {
            throw ValidationException::withMessages(['boostId' => 'BoostId is for another giveaway']);
        }
        if ($quester->boosted_at) {
            throw ValidationException::withMessages(['boostId' => 'You can only boost Once']);
        }
        $giveaway = $quester->giveaway;
        if ($giveaway->sleep_balance < 400)
            throw ValidationException::withMessages(['boostId' => 'Giveaway sleep faucet is dry']);
        $quester->pump += 1;
        $quester->sleep += 200;
        $quester->boosted_at = now();
        $quester->save();
        $quester->pumps()->create([
            'user_id' => $request->user()->id,
        ]);
        // update booster
        $boost->pump += 1;
        $boost->sleep += 200;
        $boost->save();
        $boost->pumps()->create([
            'user_id' => $request->user()->id,
        ]);
        $boost->questers()->attach($quester);
        return back();
    }
}
