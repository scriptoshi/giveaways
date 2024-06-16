<?php

namespace App\Http\Controllers;

use App\Enums\ConnectionProvider;
use App\Enums\QuestStatus;
use App\Enums\QuestType;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Quest;

use App\Models\Giveaway;
use App\Support\Discord;
use App\Support\Telegram;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class QuestsController extends Controller
{

    public function checkTask(Request $request)
    {
        $addresses = $request->get('addresses');
        $complete = Account::query()->whereIn('address', str($addresses)->explode(',')->all())->exists();
        return compact('complete');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, Giveaway $giveaway)
    {
        $request->validate([
            'username' => 'required|string',
            'groupId' => 'nullable|string',
            'live' => 'required|boolean',
            'type' => ['required', new Enum(QuestType::class)],
            'min' => ['nullable', 'min:0',  'numeric', Rule::requiredIf(in_array($request->type, ['token', 'contribute'])),],
            ...($request->type === 'token'
                ? [
                    'name' => 'required|string',
                    'decimals' => 'required|integer',
                    'chainId' => 'required|integer',
                    'symbol' => 'required|string',
                    'url' => 'required|string|url',
                ]
                : []
            ), ...($request->type === 'nft'
                ? [
                    'name' => 'required|string',
                    'chainId' => 'required|integer',
                    'symbol' => 'required|string',
                    'url' => 'required|string|url',
                ]
                : []
            ),
            ...($request->type === 'api'
                ? [
                    'url' => 'required|string|url',
                    'instruction' => 'required|string'
                ]
                : []
            ),
        ]);
        $connection = null;
        $username = null;
        $data = null;
        $min = 0;
        $questType = QuestType::from($request->type);
        $isLive = $request->live;
        if ($isLive && in_array($request->type, ['token', 'contribute', 'nft', 'api']) && $questType->min() < $giveaway->prize) {
            $isLive = false;
        }
        if ($request->type == 'discord') {
            $username =  str(str($request->username)->explode('/')->last())->explode('?')->first();
            $added = Discord::botWasAddedToInviteGuild($username);
            $connection = $request->user()->connections->where('provider', ConnectionProvider::DISCORD)->first();
            if (!$added || !$connection) throw ValidationException::withMessages(['username' => ['Discord Quest cannot be saved. Bot not authorized']]);
        }
        if ($request->type == 'twitter') {
            $username =  str(str($request->username)->explode('/')->last())->explode('?')->first();
        }
        if ($request->type == 'tweet' && $request->comment) {
            $username =  'comment';
        }
        if ($request->type == 'telegram') {
            $username =  str(str($request->username)->explode('/')->last())->explode('?')->first();
            $added = Telegram::checkBotAccess($username);
            if (!$added) throw ValidationException::withMessages(['username' => ['Telegram Quest cannot be saved. Bot not authorized']]);
        }
        if (in_array($request->type, ['token', 'contribute'])) {
            if ($isLive && $questType->min() < $giveaway->prize) $isLive = false;
            $min = $request->min;
        }
        if ($request->type == 'token') {
            $data = $request->only(['name', 'decimals', 'chainId', 'symbol', 'url']);
        }
        if ($request->type == 'nft') {
            $data = $request->only(['name', 'chainId', 'symbol', 'url']);
        }

        if ($request->type == 'api') {
            $data = $request->only(['url', 'instruction']);
        }
        $gas_per_quest = config('app.sleep.quest', 100);
        $giveaway->quests()->updateOrCreate([
            'type' => $request->type,
        ], [
            'user_id' => $request->user()->id,
            'connection_id' => $connection?->id,
            'username' => $request->username,
            'groupId' => $username ?? $request->groupId ?? null,
            'status' => $isLive ? QuestStatus::ACTIVE : QuestStatus::PENDING,
            'live' => $isLive,
            'data' => $data,
            'min' => $min,
            'gas' => $gas_per_quest
        ]);
        return  back();
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Quest $quest)
    {
        $quest->load('giveaway');
        $this->authorize('update', $quest);
        $request->validate([
            'username' => 'required|string',
            'live' => 'required|boolean',
            'groupId' => 'nullable|string',
            'min' => ['nullable', 'min:0', 'numeric', Rule::requiredIf(in_array($request->type, ['token', 'contribute'])),],
            ...($request->type === 'token'
                ? [
                    'name' => 'required|string',
                    'decimals' => 'required|integer',
                    'chainId' => 'required|integer',
                    'symbol' => 'required|string',
                    'url' => 'required|string|url',
                ]
                : []
            ), ...($request->type === 'nft'
                ? [
                    'name' => 'required|string',
                    'chainId' => 'required|integer',
                    'symbol' => 'required|string',
                    'url' => 'required|string|url',
                ]
                : []
            ),
            ...($request->type === 'api'
                ? [
                    'url' => 'required|string|url',
                    'instruction' => 'required|string'
                ]
                : []
            ),
        ]);
        $connection = null;
        $username = null;
        $data = null;
        $min = 0;
        $questType = QuestType::from($request->type);
        $isLive = $request->live;
        if ($isLive && in_array($request->type, ['token', 'contribute', 'nft', 'api']) && $questType->min() < $quest->giveaway->prize) {
            $isLive = false;
        }
        if ($request->type == 'discord') {
            $username =  str(str($request->username)->explode('/')->last())->explode('?')->first();
            $added = Discord::botWasAddedToInviteGuild($username);
            $connection = $request->user()->connections->where('provider', ConnectionProvider::DISCORD)->first();
            if (!$added || !$connection) throw ValidationException::withMessages(['username' => ['Discord Quest cannot be saved. Bot not authorized']]);
        }
        if ($request->type == 'twitter') {
            $username =  str(str($request->username)->explode('/')->last())->explode('?')->first();
        }
        if ($request->type == 'tweet' && $request->comment) {
            $username =  'comment';
        }
        if ($request->filled('telegram')) {
            $username =  str(str($request->username)->explode('/')->last())->explode('?')->first();
            $added = Telegram::checkBotAccess($username);
            if (!$added) throw ValidationException::withMessages(['username' => ['Discord Quest cannot be saved. Bot not authorized']]);
        }

        if (in_array($request->type, ['token', 'contribute'])) {
            $min = $request->min;
        }

        if ($request->type == 'token') {
            $data = $request->only(['name', 'decimals', 'chainId', 'symbol', 'url']);
        }

        if ($request->type == 'nft') {
            $data = $request->only(['name', 'chainId', 'symbol', 'url']);
        }

        if ($request->type == 'api') {
            $data = $request->only(['url', 'instruction']);
        }
        $quest->connection_id =  $connection?->id;
        $quest->username = $request->username;
        $quest->data = $data;
        $quest->min = $min;
        $quest->groupId =  $username ?? $request->groupId ?? null;
        $quest->status = $request->live ? QuestStatus::ACTIVE : QuestStatus::PENDING;
        $quest->save();
        return back();
    }
}
