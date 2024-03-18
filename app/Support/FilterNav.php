<?php

namespace App\Support;

use App\Enums\ActivityType;
use App\Enums\AirdropStatus;
use App\Enums\LaunchpadStatus;
use App\Enums\LaunchpadType;
use App\Enums\NftStatus;
use App\Enums\NftType;
use App\Enums\StakingType;
use App\Http\Resources\Activity as ResourcesActivity;
use App\Http\Resources\Airdrop as ResourcesAirdrop;
use App\Http\Resources\Launchpad as ResourcesLaunchpad;
use App\Http\Resources\Nft as ResourcesNft;
use App\Http\Resources\Staking as ResourcesStaking;
use App\Models\Token;
use App\Models\Activity;
use App\Models\Airdrop;
use App\Models\Coin;
use App\Models\Launchpad;
use App\Models\Nft;
use App\Models\Staking;
use Illuminate\Http\Request;

class FilterNav
{

    public static function staking(Request $request, array  $mainet)
    {

        $latest = $request->get('latest');
        $status = $request->get('status');
        $category = $request->get('category');
        $allchains = $request->get('allchains');
        $chainId = $request->get('chainId');
        $search = $request->get('query');
        $type = $request->get('type');
        $limit = $request->get('limit', 6);
        $query = Staking::query()
            ->with([
                'token' => function ($query) {
                    $query->withExists([
                        'badges as verified' => function ($query) {
                            $query->where('badges.badge', 'KYC');
                        }
                    ]);
                },
                'project',
            ]);
        if ($allchains == 0 && !empty($chainId)) {
            $query->where('chainId', $chainId);
        } else {
            $query->whereIn('chainId', $mainet);
        }

        if (!!$latest) {
            if ($latest == 'newest') {
                $query->latest();
            } else {
                $query->withCount('activities')
                    ->orderBy('activities_count', 'desc');
            }
        } else {
            $query->latest();
        }

        if (!empty($category)) {
            switch ($category) {
                case 'staking':
                    $query->where('type', StakingType::STAKING);
                    break;
                case 'farm':
                    $query->where('type', StakingType::SYNTHETIX);
                    break;
                default:
                    break;
            }
        }

        if (!empty($search)) {
            $query->where('contract', 'LIKE', "%$search%")
                ->orWhereHas('token', function ($qry) use ($search) {
                    $qry->where('symbol', 'LIKE', "%$search%")
                        ->orWhere('contract', 'LIKE', "%$search%")
                        ->orWhere('name', 'LIKE', "%$search%");
                });
        }
        if (auth()->check() && !!$type) {
            switch ($type) {
                case 'joined':
                    $query->whereHas('accounts', fn ($q) => $q->where('user_id', $request->user()?->id));
                    break;
                case 'mine':
                    $query->where('user_id', $request->user()?->id);
                    break;
                default:
                    break;
            }
        }
        switch ($status) {
            case 'live':
                $query->where(function ($q) {
                    $q->where('status', AirdropStatus::LIVE)
                        ->orWhere(fn ($qry) => $qry->where('start_date', '>=', now())->where('end_date', '<=', now()));
                });
                break;
            case 'pending':
                $query->where(fn ($q) => $q->where('status', AirdropStatus::PENDING)
                    ->orWhere('status', AirdropStatus::QUEUED)
                    ->orWhere('start_date', '<=', now()));
                break;
            case 'success':
                $query->where('status', AirdropStatus::SUCCESS);
                break;
            case 'failed':
                $query->where('status', AirdropStatus::FAILED);
                break;
            case 'cancelled':
                $query->where('status', AirdropStatus::CANCELLED);
                break;
            case 'ended':
                $query->where(
                    fn ($q) => $q->where('status', AirdropStatus::ENDED)
                        ->orWhere('status', AirdropStatus::SUCCESS)
                        ->orWhere('end_date', '<=', now())
                );
                break;
            default:
                break;
        }
        $results = $query->take($limit)->get();
        $contracts =  $results->flatMap(function ($stake) {
            if (isset($stake->data['stakingToken']))
                return [
                    $stake->data['stakingToken']['contract'],
                    $stake->data['stakingToken']['token0']['token'],
                    $stake->data['stakingToken']['token1']['token'],
                    $stake->data['rewardToken']['contract']
                ];
        });
        $symbols =  $results->flatMap(function ($stake) {
            if (isset($stake->data['stakingToken']))
                return [
                    $stake->data['stakingToken']['token0']['symbol'],
                    $stake->data['stakingToken']['token1']['symbol'],
                    str($stake->data['stakingToken']['token0']['symbol'])->replaceFirst('W', ''),
                    str($stake->data['stakingToken']['token1']['symbol'])->replaceFirst('W', '')
                ];
        });


        //grab known logos
        $tokens = Token::whereIn('contract', $contracts->unique()->all())->orWhereIn('symbol', $symbols->unique()->all())->pluck('logo_uri', 'symbol');
        $coins = Coin::whereIn('contract', $contracts->unique()->all())->orWhereIn('symbol', $symbols->unique()->all())->pluck('logo_uri', 'symbol');
        $logos = $tokens->merge($coins);
        return [ResourcesStaking::collection($results), $logos];
    }

    public static function airdrops(Request $request, array $mainet)
    {

        $latest = $request->get('latest');
        $category = $request->get('category');
        $allchains = $request->get('allchains');
        $chainId = $request->get('chainId');
        $search = $request->get('query');
        $status = $request->get('status');
        $type = $request->get('type');
        $limit = $request->get('limit', 6);
        $query = Airdrop::query()
            ->with([
                'token' => function ($query) {
                    $query->withExists([
                        'badges as verified' => function ($query) {
                            $query->where('badges.badge', 'KYC');
                        }
                    ]);
                },
                'project',
                'token.chain',
                'token.badges',
            ]);

        if ($allchains == 0 && !empty($chainId)) {
            $query->where('chainId', $chainId);
        } else {
            $query->whereIn('chainId', $mainet);
        }
        if (!empty($latest)) {
            if ($latest == 'trending') {
                $query->withCount('activities')
                    ->orderBy('activities_count', 'desc')
                    ->latest();
            } else {
                switch ($latest) {
                    case 'tokens':
                        $query->orderBy('tokens', 'desc');
                        break;
                    case 'claimed':
                        $query->orderBy('claimed', 'desc');
                        break;
                    default:
                        $query->latest();
                        break;
                }
            }
        } else {
            $query->latest();
        }


        if (!empty($search)) {
            $query->where('contract', 'LIKE', "%$search%")
                ->orWhereHas('token', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('contract', 'LIKE', "%$search%")
                        ->orWhere('symbol', 'LIKE', "%$search%");
                })
                ->orWhereHas('project', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                });
        }

        switch ($status) {
            case 'live':
                $query->where('status', AirdropStatus::LIVE);
                break;
            case 'pending':
                $query->where(fn ($q) => $q->where('status', AirdropStatus::PENDING)->orWhere('status', AirdropStatus::QUEUED));
                break;
            case 'success':
                $query->where('status', AirdropStatus::SUCCESS);
                break;
            case 'failed':
                $query->where('status', AirdropStatus::FAILED);
                break;
            case 'cancelled':
                $query->where('status', AirdropStatus::CANCELLED);
                break;
            case 'ended':
                $query->where(fn ($q) => $q->where('status', AirdropStatus::ENDED)->orWhere('status', AirdropStatus::SUCCESS));
                break;
            default:
                break;
        }

        switch ($category) {
            case 'nft':
                $query->whereHas('nfts');
                break;
            case 'quest':
                $query->whereHas('task');
                break;
            default:
                break;
        }

        if (auth()->check() && !!$type) {
            switch ($type) {
                case 'joined':
                    $query->whereHas('activities', fn ($q) => $q->where('user_id', $request->user()?->id));
                    break;
                case 'mine':
                    $query->where('user_id', $request->user()?->id);
                    break;
                default:
                    break;
            }
        }

        $results = $query->take($limit)->get();
        return ResourcesAirdrop::collection($results);
    }


    public static function nfts(Request $request, array  $mainet)
    {
        $status = $request->get('status');
        $latest = $request->get('latest');
        $allchains = $request->get('allchains');
        $chainId = $request->get('chainId');
        $orderby = $request->get('order_by');
        $search = $request->get('query');
        $category = $request->get('category');
        $type = $request->get('type');
        $limit = $request->get('limit', 6);
        $query = Nft::with([
            'project.socials',
            'airdrops'
        ])->withCount([
            'mints',
            'views as hits',
            'likes as likeCount',
            'reactions as loves' => function ($query) {
                $query->where('type', 'love');
            },
            'reactions as hates' => function ($query) {
                $query->where('type', 'hate');
            }
        ])->where('is_external', false);
        if ($allchains == 0 && !empty($chainId)) {
            $query->where('chainId', $chainId);
        } else {
            $query->whereIn('chainId', $mainet);
        }

        if (!!$latest) {
            if ($latest == 'newest') {
                $query->latest();
            } else {
                $query->withCount('mints')
                    ->orderBy('mints_count', 'desc');
            }
        } else {
            $query->latest();
        }


        if (auth()->check()) {
            if (!!$type) {
                switch ($type) {
                    case 'liked':
                        $query->whereHas('likes', function ($q) use ($request) {
                            $q->where('user_id', '=', $request->user()->id);
                        });
                    case 'joined':
                        $query->whereHas('mints', fn ($q) => $q->where('user_id', $request->user()?->id));
                        break;
                    case 'mine':
                        $query->where('user_id', $request->user()?->id);
                        break;
                    default:
                        break;
                }
            }
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

        if (!empty($search)) {
            $query->where('contract', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhere('symbol', 'LIKE', "%$search%")
                ->orWhere('description', 'LIKE', "%$search%")
                ->orWhereHas('project', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                });
        }
        switch ($status) {
            case 'live':
                $query->where('startDate', '<=', now())->where('endDate', '>=', now());
                break;
            case 'pending':
                $query->where('startDate', '>=', now());
                break;
            case 'ended':
                $query->where('endDate', '<=', now());
                break;
            case 'cancelled':
                $query->where('status', NftStatus::CANCELLED);
                break;
            default:
                break;
        }

        switch ($orderby) {
            case 'create_date':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price':
                $query->orderBy('fee', 'desc');
                break;
            case 'start_date':
                $query->orderBy('startDate', 'desc');
                break;
            case 'end_date':
                $query->latest('endDate');
                break;
            case 'end':
                $query->orderBy('ends_at', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        switch ($category) {
            case 'builder':
                $query->where('type', NftType::BUILDER);
                break;
            case 'collection':
                $query->where('type', NftType::COLLECTION);
                break;
            case 'membership':
                $query->where('type', NftType::MEMBERSHIP);
                break;
            case 'mine':
                $query->where('user_id', $request->user()?->id);
                break;
            default:
                break;
        }
        $total = $query->clone()->count();
        $results =  $query->take($limit)->get();
        return ['nfts' => ResourcesNft::collection($results), 'ended' => $limit > $total, 'total' => $total];
    }

    /**
     * Launchpads filter
     */
    public static function launchpads(Request $request, array $mainet)
    {

        $status = $request->get('status');
        $latest = $request->get('latest', 'trending');
        $allchains = $request->get('allchains');
        $chainId = $request->get('chainId');
        $search = $request->get('query');
        $category = $request->get('category');
        $type = $request->get('type');
        $limit = $request->get('limit', 12);

        $query = Launchpad::query()
            ->with([
                'coin',
                'user',
                'badges',
                'amm'
            ])
            ->withExists([
                'badges as verified' => function ($query) {
                    $query->where('badges.badge', 'KYC');
                }
            ])->withCount([
                'views as hits',
                'likes as likeCount',
                'reactions as loves' => function ($query) {
                    $query->where('type', 'love');
                },
                'reactions as hates' => function ($query) {
                    $query->where('type', 'hate');
                }
            ]);
        if ($allchains == 0 && !empty($chainId)) {
            $query->where('chainId', $chainId);
        } else {
            $query->whereIn('chainId', $mainet);
        }
        if (!empty($latest)) {
            if ($latest == 'newest') {
                $query->latest();
            } elseif ($latest == 'trending') {
                $query->orderBy('participants', 'desc')
                    ->latest();
            } else {
                switch ($latest) {
                    case 'hardcap':
                        $query->orderBy('harcap', 'desc');
                        break;
                    case 'softcap':
                        $query->orderBy('softcap', 'desc');
                        break;
                    case 'percent':
                        $query->orderBy('percent', 'desc');
                        break;
                    case 'start':
                        $query->latest('starts_at');
                        break;
                    case 'end':
                        $query->orderBy('ends_at', 'desc');
                        break;
                    default:
                        $query->latest();
                        break;
                }
            }
        } else {
            $query->latest();
        }

        if (!empty($search)) {
            $query->where('contract', 'LIKE', "%$search%")
                ->orWhere('token_contract', 'LIKE', "%$search%")
                ->orWhere('token_name', 'LIKE', "%$search%")
                ->orWhere('token_symbol', 'LIKE', "%$search%");
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
            if (!empty($type)) {
                switch ($type) {
                    case 'liked':
                        $query->whereHas('likes', function ($q) use ($request) {
                            $q->where('user_id', '=', $request->user()->id);
                        });
                    case 'joined':
                        $query->whereHas('accounts', fn ($q) => $q->where('user_id', $request->user()?->id));
                        break;
                    case 'mine':
                        $query->where('user_id', $request->user()?->id);
                        break;
                    default:
                        break;
                }
            }
        }

        switch ($category) {
            case 'launchpads':
                $query->where('type', LaunchpadType::LAUNCHPAD);
                break;
            case 'fairlaunch':
                $query->where('type', LaunchpadType::FAIRLAUNCH);
                break;
            case 'fairliquidity':
                $query->where('type', LaunchpadType::FAIRLIQUIDTY);
                break;
            case 'privatesale':
                $query->where('type', LaunchpadType::PRIVATESALE);
                break;
            default:
                break;
        }
        switch ($status) {
            case 'live':
                $query->where('status', LaunchpadStatus::LIVE);
                break;
            case 'pending':
                $query->where(fn ($q) => $q->where('status', LaunchpadStatus::PENDING)->orWhere('status', LaunchpadStatus::QUEUED));
                break;
            case 'success':
                $query->where('status', LaunchpadStatus::SUCCESS);
                break;
            case 'failed':
                $query->where('status', LaunchpadStatus::FAILED);
                break;
            case 'cancelled':
                $query->where('status', LaunchpadStatus::CANCELLED);
                break;
            case 'ended':
                $query->where(fn ($q) => $q->where('status', LaunchpadStatus::ENDED)->orWhere('status', LaunchpadStatus::SUCCESS));
                break;
            default:
                break;
        }

        $results =  $query->take($limit)->get();
        return ResourcesLaunchpad::collection($results);
    }
}
