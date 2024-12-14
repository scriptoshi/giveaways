<?php

namespace App\Http\Controllers;

use App\Actions\SelectWinners;
use App\Actions\Uploads;
use App\Enums\ConnectionProvider;
use App\Enums\GiveawayStatus;
use App\Enums\GiveawayType;
use App\Enums\QuesterStatus;
use App\Enums\QuestStatus;
use App\Enums\QuestType;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Coin as ResourcesCoin;
use App\Http\Resources\Connection;
use App\Http\Resources\Giveaway as GiveawayResource;
use App\Http\Resources\Launchpad;
use App\Http\Resources\Quest as ResourcesQuest;
use App\Http\Resources\Quester as QuesterResource;
use App\Http\Tags\Meta;
use App\Jobs\CheckGiveawayStatusLater;
use App\Models\Account;
use App\Models\Coin;
use App\Models\Giveaway;
use Inertia\Inertia;
use App\Models\Quest;
use App\Support\Discord;
use App\Support\Etherscan;
use App\Support\Telegram;
use App\Support\Utils;
use App\Web3\Utils as Web3Utils;
use Carbon\Carbon;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Str;
use SWeb3\Utils as SWeb3Utils;

class GiveawaysController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $order = $request->get('order', 'created');
        $by = $request->get('by', 'latest');
        $perPage = 25;
        $query = Giveaway::with(['project.logo'])
            ->withCount([
                'questers as totalParticipants',
                'quests as totalTasks'
            ]);

        if (!empty($keyword)) {
            $query->whereHas('project', fn (Builder $q) => $q->where('name', 'LIKE', "%$keyword%")->where('description', 'LIKE', "%$keyword%"));
        }
        $orderColumn = match ($order) {
            'prize' => 'prize',
            'winners' => 'num_winners',
            'joined' => 'totalParticipants',
            default => 'created_at',
        };
        if ($by == 'oldest') {
            $query->oldest($orderColumn);
        } else {
            $query->latest($orderColumn);
        }
        $giveawaysItems = $query->paginate($perPage);
        Meta::addMeta('title', __('Latest Giveaways'));
        Meta::addMeta('keywords', __('audited crypto giveaway, usdt giveaway, crypto giveaway reviews and comments, questing campaigns, Like, follow retweet giveaway, simple giveaway tasks, gas, tokens'));
        Meta::addMeta('description', __('Grab the newests crypto giveaways on giveawaysfinance.  Giveaways Finance lists the audited and verified crypto giveaways.  Earn crypto by simply completing 3 - 5 simple tasks liek retweet, follow and  like.'));
        return Inertia::render('Giveaways/Index', [
            'giveaways' => GiveawayResource::collection($giveawaysItems),
            'popular' => function () use ($request) {
                $list = Giveaway::with(['project.logo'])
                    ->withCount('questers as totalParticipants')
                    ->latest()
                    ->take(10)
                    ->get();
                return GiveawayResource::collection($list);
            }
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {

        return Inertia::render('Giveaways/Create', [
            'hasProject' => function () use ($request) {
                if (!auth()->check()) return false;
                return $request->user()->project()->exists();
            },
            'usdtContracts' => config('app.usdt'),
            'prizeClaim' => fn () => json_decode(\File::get(resource_path('js/abi/PrizeClaim.json')))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        // ONLY SUPPORTS BNB DEPOSIT FOR NOW

        $upload = app(Uploads::class);
        $projectExists = $request->user()->project()->exists();
        if (!$request->user()->isAdmin() && $request->chainId != 56) {
            return back()->with('error', 'Invalid transaction hash. Only BNB supported');
        }
        $prizeClaim = json_decode(\File::get(resource_path('js/abi/PrizeClaim.json')), true);
        $usdt = config('app.usdt');
        $amount = $request->amount;
        $status = GiveawayStatus::UNPAID;
        $claimAddress = $prizeClaim['addresses'][$request->chainId];
        $tx = Etherscan::getTokenTransfer($request->chainId, $usdt[(int) $request->chainId], $request->hash, $request->account);
        if (isset($tx->to)) { // if tx is ready save everyone pain.
            if (SWeb3Utils::toChecksumAddress($tx->to) != SWeb3Utils::toChecksumAddress($claimAddress)) return back()->with('error', 'Invalid transaction hash');
            $amount = Web3Utils::toBTC($tx->value, $tx->tokenDecimal);
            $status = GiveawayStatus::PAID;
        }
        $request->validate([
            'duration' => 'required|numeric',
            'period' => 'required|string',
            'starts_at' => 'date|required',
            'symbol' => 'required|string',
            'hash' => 'required|string',
            'num_winners' => 'required|numeric',
            'type' => ['required', 'string', new Enum(GiveawayType::class)],
            // !project
            ...$projectExists
                ? []
                : [
                    'name' => 'required|string',
                    'description' => 'required|string',
                    ...$upload->validation('logo'),
                ],
            //tx
            'chainId' => 'required|numeric|min:1',
            'account' => 'required|string',
            'token' => 'required|string',
            // tasks
            'discord' => 'nullable|string|url',
            'twitter' => 'nullable|string|url',
            'telegram' => 'nullable|string|url',
            
        ]);
        $project = $request->user()->project()->firstOrCreate([], [
            'name' => $request->name,
            'rank' => 1,
            'description' => $request->description,
            'slug' => Str::slug($request->name)
        ]);
        // save project logo
        if (!$projectExists) $upload->upload($request,  $project, 'logo');
        $gasPrice = 1000;
        $giveaway = new Giveaway;
        $giveaway->project_id =  $project->id;
        $giveaway->brief = $request->brief;
        $giveaway->starts_at = Carbon::parse($request->starts_at);
        $giveaway->duration = $request->duration;
        $giveaway->period = $request->period;
        $giveaway->ends_at = Carbon::parse($request->starts_at)
            ->addSeconds(Utils::durationToSeconds($request->duration, $request->period));
        $giveaway->prize = $amount / ($request->num_winners * 2);
        $giveaway->slug = Str::slug($request->brief);
        $giveaway->fee =  $amount / 2;
        $giveaway->gas = $giveaway->fee * $gasPrice;
        $giveaway->gas_balance = $giveaway->gas;
        $giveaway->symbol = 'USDT';
        $giveaway->hash = $request->hash;
        $giveaway->num_winners = $request->num_winners;
        $giveaway->num_claimed = 0;
        $giveaway->type = $request->type;
        $giveaway->draw_size = 100;
        $giveaway->live = false;
        $giveaway->paid = $amount ?? 0;
        $giveaway->status = $status;
        $giveaway->chainId = $request->chainId;
        $giveaway->account = $request->account;
        $giveaway->save();
        $brief = value(function () use ($giveaway) {
            $total = $giveaway->prize * $giveaway->num_winners * 2;
            return Str::slug("{$total}USDT prize for {$giveaway->num_winners} winners to claim {$giveaway->prize}USDT each");
        });
        $giveaway->slug = $giveaway->slug . '-' . $giveaway->id . '-' . $brief;
        $giveaway->save();
        $gas_per_quest = config('app.quest', 100);
        if ($request->filled('discord')) {
            $inviteId =  str(str($request->discord)->explode('/')->last())->explode('?')->first();
            $added = Discord::botWasAddedToInviteGuild($inviteId);
            $connection = $request->user()->connections->where('provider', ConnectionProvider::DISCORD)->first();
            if ($added) {
                $giveaway->quests()->updateOrCreate([
                    'user_id' => $request->user()->id,
                    'connection_id' => $connection?->id ?? null,
                    'username' => $request->discord,
                    'groupId' => $inviteId,
                    'type' => QuestType::DISCORD,
                    'status' => QuestStatus::ACTIVE,
                    'min' => 0,
                    'gas' => $gas_per_quest
                ]);
            } else {
                $errors['discord'] = 'Discord Quest was not saved. Bot not authorized';
            }
        }
        if ($request->filled('twitter')) {
            $username =  str(str($request->twitter)->explode('/')->last())->explode('?')->first();
            $giveaway->quests()->updateOrCreate([
                'user_id' => $request->user()->id,
                'connection_id' => null,
                'username' => $request->twitter,
                'groupId' =>  $username,
                'type' => QuestType::TWITTER,
                'status' => QuestStatus::ACTIVE,
                'min' => 0,
                'gas' =>  $gas_per_quest
            ]);
        }
        if ($request->filled('telegram')) {
            $groupName =  str(str($request->telegram)->explode('/')->last())->explode('?')->first();
            $added = Telegram::checkBotAccess($groupName);
            if ($added) {
                $giveaway->quests()->updateOrCreate([
                    'user_id' => $request->user()->id,
                    'connection_id' => null,
                    'username' => $request->telegram,
                    'groupId' => $groupName,
                    'type' => QuestType::TELEGRAM,
                    'status' => QuestStatus::ACTIVE,
                    'min' => 0,
                    'gas' =>  $gas_per_quest
                ]);
            } else {
                $errors['telegram'] = 'Telegram Quest was not saved. Bot not authorized';
            }
        }
        if ($status != GiveawayStatus::PAID) {
            // check again after a minute
            CheckGiveawayStatusLater::dispatch($giveaway)
                ->delay(now()->addMinute());
        }
        return redirect()
            ->route('giveaways.tasks', ['giveaway' => $giveaway->uuid])
            ->with('success', 'Giveaway added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Giveaway  $giveaway)
    {
        $giveaway->load([
            'project.logo',
            'questers' => fn (HasMany $q) => $q->limit(10),
        ]);
        $giveaway->loadCount([
            'questers as totalParticipants',
            'quests as totalTasks'
        ]);
        $select = ["*", \DB::raw("RANK() OVER (ORDER BY gas DESC) as 'rank'")];
        $quests = auth()->check()
            ?  $giveaway->quests()
            ->where('status', QuestStatus::ACTIVE)
            ->withExists([
                'tasks as complete' => function (Builder $query) use ($request) {
                    $query->where('user_id', $request->user()->id)
                        ->where('status', TaskStatus::COMPLETE);
                }
            ])
            ->with(['tasks' => function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            }])
            ->get()
            :  $giveaway->quests()
            ->where('status', QuestStatus::ACTIVE)
            ->get(); // tasks
        $query = $giveaway->questers() // leaderboard
            ->select($select)
            ->withCount(
                [
                    'pumps as pumps_24' => fn (Builder $q) => $q->where('created_at', '>', now()->subDay()),
                    'pumps as pumps_7' => fn (Builder $q) => $q->where('created_at', '>', now()->subDays(7)),
                    'pumps as pumps_30' => fn (Builder $q) => $q->where('created_at', '>', now()->subDays(30)),
                ]
            );
        match ($giveaway->type) {
            GiveawayType::FCFS,
            GiveawayType::DRAW_FCFS => $query->oldest(),
            GiveawayType::DRAW,
            GiveawayType::LEADERBOARD,
            GiveawayType::DRAW_LEADERBOARD,
            GiveawayType::FCFS_LEADERBOARD => $query->oldest('rank'),
            default => $query->oldest(),
        };
        $questers = $query->with('user.account')->take(5)->get();
        $launchpad = $giveaway->project->launchpad()->first();
        $summary = value(function () use ($giveaway) {
            $total = $giveaway->prize * $giveaway->num_winners * 2;
            $tasks = $giveaway->totalTasks ?? 0;
            return  "@{$giveaway->project->name} giveaway on giveawaysfinance. Total {$total} USDT for {$giveaway->num_winners} winners each taking {$giveaway->prize} USDT.  Only {$tasks} Tasks to join";
        });
        $title = value(function () use ($giveaway) {
            $total = $giveaway->prize * $giveaway->num_winners * 2;
            return  "{$total} USDT giveaway by @{$giveaway->project->name}";
        });
        $prize = $giveaway->prize * 1;
        $gas =  $giveaway->gas * 1;
        Meta::addMeta('title', $title);
        Meta::addMeta('keywords', "$prize USDT, $gas GAS, " . __('usdt, crypto giveaway, usdt giveaway, reviews , comments, questing, like, follow, retweet, giveaways.finance'));
        Meta::addMeta('description', $summary);
        return Inertia::render('Giveaways/Show', [
            'giveaway' =>  new GiveawayResource($giveaway),
            'questers' =>   QuesterResource::collection($questers),
            'winners' => function () use ($giveaway) {
                $winners = $giveaway->questers()
                    ->with('user.account')
                    ->where('status', QuesterStatus::WINNER)
                    ->orWhere('status', QuesterStatus::CLAIMED)
                    ->get();
                return  QuesterResource::collection($winners);
            },
            'quester' => function () use ($giveaway, $select, $request) {
                if (!auth()->check()) return null;
                $quester =  $giveaway->questers()
                    ->select($select)
                    ->withCount(
                        [
                            'pumps as pumps_24' => fn (Builder $q) => $q->where('created_at', '>', now()->subDay()),
                            'pumps as pumps_7' => fn (Builder $q) => $q->where('created_at', '>', now()->subDays(7)),
                            'pumps as pumps_30' => fn (Builder $q) => $q->where('created_at', '>', now()->subDays(30)),
                        ]
                    )
                    ->with('user.account')
                    ->where('user_id', $request->user()->id)
                    ->first();
                return $quester ? new QuesterResource($quester) : null;
            },
            'twitter' => fn () => static::findQuest($quests, QuestType::TWITTER),
            'telegram' => fn () => static::findQuest($quests, QuestType::TELEGRAM),
            'discord' => fn () => static::findQuest($quests, QuestType::DISCORD),
            'tweet' => fn () => static::findQuest($quests, QuestType::TWEET),
            'mint' => fn () => static::findQuest($quests, QuestType::MINT),
            'swap' => fn () => static::findQuest($quests, QuestType::SWAP),
            'contribute' => fn () => static::findQuest($quests, QuestType::CONTRIBUTE),
            'api' => fn () => static::findQuest($quests, QuestType::API),
            'token' => fn () => static::findQuest($quests, QuestType::TOKEN),
            'nft' => fn () => static::findQuest($quests, QuestType::NFT),
            'launchpad' => function () use ($launchpad) {
                if (!$launchpad) return null;
                return new Launchpad($launchpad);
            },
            'coin' => function () use ($launchpad) {
                if (!$launchpad) return null;
                $coin = Coin::query()->where('chainId', $launchpad->chainId)->where('is_native', true)->first();
                return new ResourcesCoin($coin);
            },
            'contribution' => function () use ($launchpad, $request) {
                if (!$launchpad || !auth()->check()) return 0;
                return $launchpad->contributions()->where('user_id', $request->user()->id)->sum('amount');
            },
            'connections' => [
                'discord' => auth()->check() ? $request->user()->connections()->where('provider', ConnectionProvider::DISCORD)->exists() : false,
                'twitter' => auth()->check() ? $request->user()->connections()->where('provider', ConnectionProvider::TWITTER)->exists() : false,
                'telegram' => auth()->check() ? $request->user()->connections()->where('provider', ConnectionProvider::TELEGRAM)->exists() : false,
            ],
            'prizeClaim' => json_decode(\File::get(resource_path('js/abi/PrizeClaim.json')), true)

        ]);
    }

    /**
     * GasQuestBot
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Giveaway $giveaway)
    {
        $giveaway->load(['project', 'quests']);
        $this->authorize('update', $giveaway->project);
        return Inertia::render('Giveaways/Edit', [
            'giveaway' => new GiveawayResource($giveaway),
            'usdtContracts' => config('app.usdt'),
            'prizeClaim' => json_decode(\File::get(resource_path('js/abi/PrizeClaim.json')), true)
        ]);
    }

    private static function findQuest($quests, QuestType $type): ResourcesQuest|null
    {
        $quest = $quests->first(fn (Quest $quest) => $quest->type == $type);
        if (!$quest) return null;
        return  new ResourcesQuest($quest);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function tasks(Request $request, Giveaway  $giveaway)
    {

        $giveaway->load([
            'project.logo',
            'project.socials'
        ]);
        $this->authorize('update', $giveaway->project);
        $quests =  $giveaway->quests()->get();
        $chain = (object)[
            'SEPOLIA' => 11155111,
            'BSC' => 56,
            'BSC_TESTNET' => 97,
            'ARBITRUM' => 42161,
            'BASE' => 8453,
            'POLYGON' => 137,
            'AVALANCHE' => 43114
        ];
        // nonfungiblepositionmanager
        $NFPM = [
            $chain->SEPOLIA => '0x5bE4DAa6982C69aD20A57F1e68cBcA3D37de6207',
            $chain->BSC =>  '0x7b8A01B39D58278b5DE7e48c8449c9f4F5170613',
            $chain->BSC_TESTNET =>  '0xF235795E939A2A6076E82B8434649f5BcF9B9637',
            $chain->ARBITRUM =>  '0xC36442b4a4522E871399CD717aBDD847Ab11FE88',
            $chain->BASE =>  '0x03a520b32C04BF3bEEf7BEb72E919cf822Ed34f1',
            $chain->POLYGON =>  '0xC36442b4a4522E871399CD717aBDD847Ab11FE88',
            $chain->AVALANCHE =>  '0x655C406EBFa14EE2006250925e54ec43AD184f8B',
        ];

        $WETH9 = [
            $chain->SEPOLIA =>  '0xfFf9976782d46CC05630D1f6eBAb18b2324d6B14',
            $chain->BSC =>  '0xbb4CdB9CBd36B01bD1cBaEBF2De08d9173bc095c',
            $chain->BSC_TESTNET =>  '0xae13d989daC2f0dEbFf460aC112a837C89BAa7cd',
            $chain->ARBITRUM =>  '0x82aF49447D8a07e3bd95BD0d56f35241523fBab1',
            $chain->BASE =>  '0x4200000000000000000000000000000000000006',
            $chain->POLYGON =>  '0x0d500B1d8E8eF31E21C99d1Db9A6444d3ADf1270',
            $chain->AVALANCHE =>  '0xB31f66AA3C1e785363F0875A1B74E27b85FD66c7',
        ];
        $abi = json_decode(File::get(resource_path('js/abi/FairFactory.json')));
        return Inertia::render('Giveaways/Tasks', [
            'mins' => collect(QuestType::cases())->mapWithKeys(fn (QuestType $q) => [$q->value => $q->min()]),
            'giveaway' => new GiveawayResource($giveaway),
            'twitter' => fn () => static::findQuest($quests, QuestType::TWITTER),
            'telegram' => fn () => static::findQuest($quests, QuestType::TELEGRAM),
            'discord' => fn () => static::findQuest($quests, QuestType::DISCORD),
            'tweet' => fn () => static::findQuest($quests, QuestType::TWEET),
            'mint' => fn () => static::findQuest($quests, QuestType::MINT),
            'swap' => fn () => static::findQuest($quests, QuestType::SWAP),
            'contribute' => fn () => static::findQuest($quests, QuestType::CONTRIBUTE),
            'api' => fn () => static::findQuest($quests, QuestType::API),
            'token' => fn () => static::findQuest($quests, QuestType::TOKEN),
            'nft' => fn () => static::findQuest($quests, QuestType::NFT),
            'launchpad' => function () use ($giveaway) {
                $launchpad = $giveaway->project->launchpad()->first();
                if (!$launchpad) return null;
                return new Launchpad($launchpad);
            },
            'factory' => $abi,
            'NFPM' => $NFPM,
            'WETH9' => $WETH9
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
    public function update(Request $request, Giveaway $giveaway)
    {
        $giveaway->load(['project']);
        $this->authorize('update', $giveaway->project);
        $request->validate([
            'duration' => 'required|numeric',
            'period' => 'required|string',
            'starts_at' => 'date|required',
            'brief' => 'string|required',
            'type' => ['required', 'string', new Enum(GiveawayType::class)],
        ]);
        if ($giveaway->starts_at > now()) {
            $giveaway->starts_at = Carbon::parse($request->starts_at);
            $giveaway->duration = $request->duration;
            $giveaway->period = $request->period;
            $giveaway->ends_at = Carbon::parse($request->starts_at)
                ->addSeconds(Utils::durationToSeconds($request->duration, $request->period));
        }
        $giveaway->brief = $request->brief;
        $giveaway->type = $request->type;
        $giveaway->save();
        return back()->with('success', 'Giveaway updated!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function bonusCode(Request $request, Giveaway $giveaway)
    {
        $giveaway->load(['project']);
        $this->authorize('update', $giveaway->project);
        $request->validate(['code' => 'string|required']);
        $exists = Account::where('user_id', '!=', $request->user()->id)
            ->codeExists($request->code);
        if (!!$giveaway->project->code) throw ValidationException::withMessages(['code' => ['You can only topup a faucet once']]);
        if (!$exists) throw ValidationException::withMessages(['code' => ['Invalid Bonus Code']]);
        if ($giveaway->paid < 50) throw ValidationException::withMessages(['code' => ['Minimum Claim is for 50 USDT Giveaway']]);
        $giveaway->gas_balance += 25000;
        $giveaway->save();
        $giveaway->project->code = $request->code;
        $giveaway->project->save();
        return back()->with('success', '25000 GAS was added to your giveaway faucet!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function stop(Request $request, Giveaway $giveaway)
    {
        $giveaway->load(['project']);
        $this->authorize('update', $giveaway->project);
        $giveaway->stopped_at = now();
        $giveaway->save();
        app(SelectWinners::class)->selectWinnerFor($giveaway);
        return back()->with('success', 'Giveaway Stopped and Possible winners selected!');
    }
}
