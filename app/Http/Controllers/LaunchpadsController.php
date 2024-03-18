<?php

namespace App\Http\Controllers;

use App\Actions\Upload;
use App\Enums\FactoryType;
use App\Enums\LaunchpadStatus;
use App\Enums\LaunchpadType;
use App\Enums\LaunchpadUnsold_tokens;
use App\Http\Controllers\Controller;
use App\Http\Resources\Amm as ResourcesAmm;
use App\Http\Resources\Coin as ResourcesCoin;
use App\Http\Resources\Factory as ResourcesFactory;
use App\Http\Resources\Launchpad as LaunchpadResource;
use App\Http\Resources\Token as ResourcesToken;
use App\Models\Launchpad;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Coin;
use App\Models\Token;
use App\Models\Amm;
use App\Models\Chain;
use App\Models\Factory;
use App\Support\FilterNav;
use App\Web3\Utils as Web3Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Enum;

class LaunchpadsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $mainet = Chain::where('testnet', false)->pluck('chainId')->all();
        $launchpads = FilterNav::launchpads($request, $mainet);
        $ended = $request->get('limit', 12) >  $launchpads->count();
        return Inertia::render('Launchpads/Index', compact('launchpads', 'ended'));
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function extras(Request $request)
    {
        $promoItems = Launchpad::where('chainId', $request->chainId)->with([
            'coin',
            'token',
        ])->whereHas('badges', function ($query) {
            $query->where('badge', 'FEATURE');
        })->take(10)->get();
        $trendingItems = Launchpad::where('chainId', $request->chainId)->with([
            'token',
        ])->orderBy('participants', 'desc')
            ->take(10)
            ->get();
        $promotions = LaunchpadResource::collection($promoItems);
        $trending = LaunchpadResource::collection($trendingItems);
        return  compact('promotions', 'trending');
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request,  $type, $chainId)
    {
        $type = LaunchpadType::from($type);
        return Inertia::render('Launchpads/Create', [
            'coins' => function () use ($chainId) {
                $coinsList = Coin::where('chainId', $chainId)->get();
                return ResourcesCoin::collection($coinsList);
            },
            'tokens' => function () use ($chainId, $request) {
                if (!auth()->check()) return [];
                $tokensList = Token::query()
                    ->where('user_id', $request->user()->id)
                    ->where('chainId', $chainId)
                    ->get();
                return  ResourcesToken::collection($tokensList);
            },
            'amms' => function () use ($chainId) {
                $ammsList = Amm::where('chainId', $chainId)->get();
                return ResourcesAmm::collection($ammsList);
            },
            'factory' => function () use ($type, $chainId) {
                $factoryModel = Factory::where('type', $type->getFactory())
                    ->where('chainId', $chainId)
                    ->latest('version')
                    ->first();
                return $factoryModel ? new ResourcesFactory($factoryModel) : null;
            },
            'chainId' => $chainId,
            'type' => $type,
            'isFairLaunch' => $type->isFairLaunch() || $type->isFairLiquidity(),
            'isPrivatesale' => $type->isPrivatesale(),
            'isFairLiquidity' => $type->isFairLiquidity(),
            'isPresale' => $type->isPresale(),
        ]);
    }


    /**
     * Store a newly created launchpad in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function store(Request $request)
    {
        $request->validate([
            'factory_id' => 'required|integer|exists:factories,id',
            'amm_id' => 'required|integer|exists:amms,id',
            'presaleInfo' => 'required|array',
            'lock' => 'required|array',
            'clone' => 'required|string',
            'status_code' => 'required|numeric',
            'token' => 'required|string',
            'txhash' => 'string|nullable',
            'owner' => 'string|nullable',
            'creator' => 'string|nullable',
            'type' => ['string', 'required', new Enum(LaunchpadType::class)],
            'token_name' => 'string|required',
            'token_symbol' => 'string|required',
            'token_decimals' => 'numeric|required',
            'token_supply' => 'numeric|required',
            'logo_uri' => 'string|required',
            'upload_logo' => 'boolean|required'
        ]);
        $info = (object)$request->presaleInfo;
        $lock = (object)$request->lock;
        $factory = Factory::find($request->factory_id);
        $launchpad = Launchpad::where('chainId', $request->chainId)
            ->where('contract', $request->clone)
            ->first();
        return  redirect()->route('launchpads.edit', ['chainId' => $launchpad->chainId, 'launchpad' => $launchpad->contract]);
        // Create new Launchpad
        $amm = Amm::findOrFail($request->amm_id);
        $base = Coin::where('chainId', $request->chainId)
            ->where('contract', $info->baseToken)
            ->first();
        $launchpad = new Launchpad;
        $launchpad->user_id = $request->user()->id;
        $launchpad->coin_id = $base->id;
        $launchpad->factory_id = $factory->id;
        // Token information
        $launchpad->token_name =  $request->token_name;
        $launchpad->token_symbol =  $request->token_symbol;
        $launchpad->token_decimals =  $request->token_decimals;
        $launchpad->token_supply =  $request->token_supply;
        $launchpad->logo_uri = $request->upload_logo
            ? (config('app.uploads_disk') === 'public'
                ? app(Upload::class)->upload($request->logo_uri)
                : $request->logo_uri)
            : $request->logo_uri;
        // launchpad information
        $launchpad->amm_id = $amm->id;
        $launchpad->chainId = $factory->chainId;
        $launchpad->listing_price = Web3Utils::toBTC($info->listingRate ?? 0, $request->token_decimals);
        $launchpad->presale_price =  Web3Utils::toBTC($info->tokenPrice ?? 0, $request->token_decimals);
        $launchpad->softcap = Web3Utils::toBTC($info->softcap ?? 0, $base->decimals);
        $launchpad->hardcap = Web3Utils::toBTC($info->hardcap ?? 0, $base->decimals);
        $launchpad->percent = 0;
        $launchpad->min = Web3Utils::toBTC($info->minSpendPerBuyer, $base->decimals);
        $launchpad->max =  Web3Utils::toBTC($info->maxSpendPerBuyer, $base->decimals);
        $launchpad->presale_amount = bcdiv(bcmul((string)$launchpad->presale_price, (string)$launchpad->hardcap), (string)pow(10, $base->decimals));
        $launchpad->contract = $request->clone;
        $launchpad->unsold_tokens = $info->burn ? LaunchpadUnsold_tokens::BURN : LaunchpadUnsold_tokens::REFUND;
        $launchpad->type = $request->type;
        $launchpad->starts_at = Carbon::createFromTimestamp($info->startTime);
        $launchpad->ends_at = $launchpad->starts_at->addDays($info->presalePeriod);
        $launchpad->txhash = $request->txhash;
        $launchpad->version = $factory->version;
        $launchpad->status = LaunchpadStatus::QUEUED;
        $launchpad->total =  Web3Utils::toBTC($info->total ?? 0, $request->token_decimals);
        $launchpad->lock_period = $lock->lockPeriod;
        $launchpad->percentage = 0;
        $launchpad->liquidity_percent = Web3Utils::toBTC($info->liquidityPercent ?? 0, 2);
        $launchpad->status_code = $request->status_code;
        $launchpad->save();
        return  redirect()->route('launchpads.edit', ['chainId' => $launchpad->chainId, 'launchpad' => $launchpad->contract]);
    }


    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $chainId, $contract)
    {
        $query = Launchpad::with([
            'coin',
            'user',
            'badges',
            'socials',
            'whitelist',
            'amm',
            'factory'
        ])->where('chainId', $chainId)
            ->where('contract', $contract)
            ->withCount([
                'views as hits',
                'likes as likeCount',
                'reactions as loves' => function ($query) {
                    $query->where('type', 'love');
                },
                'reactions as hates' => function ($query) {
                    $query->where('type', 'hate');
                }
            ]);

        if (auth()->check() && $request->filled('liked') && $request->liked) {
            $query->whereHas('likes', function ($q) use ($request) {
                $q->where('user_id', '=', $request->user()->id);
            });
        }
        if (auth()->check()) {
            $query->withExists([
                'subscribers as subscribed' => function ($query) use ($request) {
                    $query->where('user_id', $request->user()?->id);
                },
                'reactions as loved' => function ($query) use ($request) {
                    $query->where('user_id', $request->user()?->id)->where('type', 'love');
                },
                'reactions as hated' => function ($query) use ($request) {
                    $query->where('user_id', $request->user()?->id)->where('type', 'hate');
                }
            ]);
        }
        $launchpadItem = $query->firstOrFail();
        views($launchpadItem)->record();
        $launchpad = new LaunchpadResource($launchpadItem);
        $lockFactory = Factory::where('type', FactoryType::LOCKFACTORY)->where('chainId', $chainId)->first();
        $lock = new ResourcesFactory($lockFactory);
        $refCount = 0;
        if (auth()->check()) {
            $accounts = $request->user()->accounts()->pluck('address')->all();
            $refCount = User::query()->whereIn('referral', $accounts)->count();
        }
        return Inertia::render('Launchpads/Show', compact('launchpad', 'lock', 'refCount', 'chainId'));
    }



    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $launchpad = Launchpad::findOrFail($id);
        $users = User::pluck('id', 'name')->all();
        $coins = Coin::pluck('id', 'name')->all();
        $tokens = Token::pluck('id', 'name')->all();
        $amms = Amm::pluck('id', 'name')->all();
        $launchpadResource = new LaunchpadResource($launchpad);
        return Inertia::render('Launchpads/Edit', compact('launchpadResource'));
    }
    /**
     * finalize the launchpad.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finalize(Request $request, Launchpad $launchpad)
    {
        if ($launchpad->user_id === $request->user()->id) {
            $launchpad->pair = $request->pair;
            $launchpad->is_finalized = true;
            $launchpad->status = LaunchpadStatus::SUCCESS;
            $launchpad->save();
        }
        return back();
    }

    /**
     * Add alike for the launchpad.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function like(Request $request, Launchpad $launchpad)
    {
        $launchpad->like();
        return ['liked' => true];
    }

    /**
     * unlike a liked launchpad.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unlike(Request $request, Launchpad $launchpad)
    {
        $launchpad->unlike();
        return ['liked' => false];
    }

    /**
     * thumbs up.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function love(Request $request, Launchpad $launchpad)
    {
        $launchpad->react('love');
        $launchpad->loadCount([
            'reactions as hates' => function ($query) {
                $query->where('type', 'hate');
            },
            'reactions as loves' => function ($query) {
                $query->where('type', 'love');
            },
        ])->loadExists([
            'reactions as loved' => function ($query) use ($request) {
                $query->where('user_id', $request->user()?->id)->where('type', 'love');
            },
            'reactions as hated' => function ($query) use ($request) {
                $query->where('user_id', $request->user()?->id)->where('type', 'hate');
            }
        ]);
        return $launchpad->only([
            'hates', 'hated', 'loved', 'loves'
        ]);
    }

    /**
     * thumbs down.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hate(Request $request, Launchpad $launchpad)
    {
        $launchpad->react('hate');
        $launchpad->loadCount([
            'reactions as hates' => function ($query) {
                $query->where('type', 'hate');
            },
            'reactions as loves' => function ($query) {
                $query->where('type', 'love');
            },
        ])->loadExists([
            'reactions as loved' => function ($query) use ($request) {
                $query->where('user_id', $request->user()?->id)->where('type', 'love');
            },
            'reactions as hated' => function ($query) use ($request) {
                $query->where('user_id', $request->user()?->id)->where('type', 'hate');
            }
        ]);
        return $launchpad->only([
            'hates', 'hated', 'loved', 'loves'
        ]);
    }

    /**
     * get notified on launchpad updates.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request,  $id)
    {
        $launchpad = Launchpad::findOrFail($id);
        $request->user()->subscriptions()->attach($id);
        $launchpad->loadExists([
            'subscribers as subscribed' => function ($query) use ($request) {
                $query->where('user_id', $request->user()?->id);
            },

        ]);
        return $launchpad->only(['subscribed']);
    }

    /**
     * cancel launchpad updates.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unsubscribe(Request $request,  $id)
    {
        $launchpad = Launchpad::findOrFail($id);
        $request->user()->subscriptions()->detach($id);
        $launchpad->loadExists([
            'subscribers as subscribed' => function ($query) use ($request) {
                $query->where('user_id', $request->user()?->id);
            },

        ]);
        return $launchpad->only(['subscribed']);
    }


    /**
     * update launchpad status.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status(Request $request,  Launchpad $launchpad)
    {

        $status = match ($request->code) {
            0 => LaunchpadStatus::QUEUED,
            1 => LaunchpadStatus::LIVE,
            2 => LaunchpadStatus::SUCCESS,
            3 => LaunchpadStatus::FAILED,
            4 => LaunchpadStatus::CANCELLED
        };
        if ($status != $launchpad->status) {
            $launchpad->status = $status;
            $launchpad->save();
            try {
                $this->sendNotifications($launchpad);
            } catch (\Exception $e) {
            }
        }
        $sale = (object)$request->status;
        $token = (object)$request->token;
        $launchpad->base_deposited =  $sale->totalBaseFormated;
        $launchpad->total_tokens =  Web3Utils::toBTC($sale->totalTokens ?? 0, $token->decimals);
        $launchpad->percent = $request->percent;
        $launchpad->save();
        return $launchpad;
    }

    /**
     * update launchpad status.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function statuses(Request $request)
    {
        $statuses = $request->statuses;
        if (config('app.env') == 'local') { // telegram doesnt allow localhost
            URL::forceRootUrl(config('app.url'));
        }
        foreach ($statuses as $status_info) {
            $status = match ($status_info['code']) {
                0 => LaunchpadStatus::QUEUED,
                1 => LaunchpadStatus::LIVE,
                2 => LaunchpadStatus::SUCCESS,
                3 => LaunchpadStatus::FAILED,
                4 => LaunchpadStatus::CANCELLED
            };
            $launchpad = Launchpad::find($status_info['id']);
            if ($launchpad && $status != $launchpad?->status) {
                $launchpad->status = $status;
                $launchpad->save();
                $this->sendNotifications($launchpad);
            }
            $data = (object)$status_info;
        }
        return 'statuses updated';
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        Launchpad::destroy($id);
        return redirect()->route('launch.launchpads.index')->with('success', 'Launchpad deleted!');
    }
}
