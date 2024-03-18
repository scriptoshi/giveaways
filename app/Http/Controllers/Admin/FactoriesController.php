<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FactoryType;
use App\Helpers\Foundry;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Resources\Factory as FactoryResource;
use App\Models\Chain;
use App\Models\Factory;
use DB;
use Inertia\Inertia;
use Illuminate\Http\Request;


class FactoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {

        $keyword = $request->get('search');
        $chains = $request->get('chain');
        $perPage = 25;
        $query = Factory::query();
        if (!empty($keyword)) {
            if (str($keyword)->startsWith('0x')) {
                $query->orWhere('contract', 'LIKE', "%$keyword%");
            } else {
                $query->where('type', 'LIKE', "%$keyword%");
            }
        }
        if (!empty($chains)) {
            $chns = str($chains)->explode(',');
            $query->whereIn('chainId', $chns);
        }
        $chainIds = Chain::pluck('chainId')->all();
        $factoriesItems = $query->whereIn('chainId', $chainIds)->latest()->paginate($perPage);
        $factories = FactoryResource::collection($factoriesItems);
        $counts = Factory::select(
            'chainId',
            'type',
            DB::raw('COUNT(*) as totals')
        )
            ->groupBy('chainId', 'type')
            ->get()->groupBy('chainId');
        return Inertia::render('Admin/Factories/Index', compact('factories', 'counts'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create(Request $request, $factory)
    {
        $type = Slug::factoryType($factory);
        $foundryInfo = $type->foundryInfo();
        $isLaunchPad = $type->isLaunchpad();
        $name = $type->name();
        $locks = Factory::where('type', FactoryType::LOCKFACTORY)->pluck('contract', 'chainId')->all();
        return Inertia::render('Admin/Factories/Create', compact('foundryInfo', 'type', 'name', 'isLaunchPad', 'locks'));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $request->validate([
            'foundry' => 'required|string',
            'chainId' => 'required|numeric',
            'contract' => 'required|string',
            'type' => 'required|string|in:PrivateSaleFoundry,AirdropFoundry,BadgeFoundry,GovernorFoundry,MultiSenderFoundry,PresaleFoundry,FairLaunchFoundry,TaxTokenFoundry,StakingFoundry,StakingRewardsFoundry,TokenFoundry,LockFoundry,DexFoundry,MigratorFoundry,LzTokenFoundry,NftFoundry'
        ]);
        $factoryType = Foundry::factoryType($request->type);
        $foundryInfo = $factoryType->foundryInfo();
        $chain = Chain::where('chainId', $request->chainId)->firstOrFail();
        Factory::query()->updateOrCreate([
            'chainId' => $chain->chainId,
            'contract' => $request->contract,
        ], [
            'chain_id' =>  $chain->id,
            'foundry' => $request->foundry,
            'version' => $foundryInfo->versions->contract ?? 1,
            'factory_version' => $foundryInfo->versions->factory,
            'abi' => $foundryInfo->abi->contract ?? [],
            'factory_abi' => $foundryInfo->abi->factory,
            'type' => $factoryType,
        ]);
        return redirect()->route('admin.factories.index')->with('success', 'Factory added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show(Request $request, $id)
    {
        $factoryItem = Factory::findOrFail($id);
        $factory = new FactoryResource($factoryItem);
        $chainId = $factoryItem->chainId;
        $search = $request->get('search');
        $deployments = [];
        switch ($factoryItem->type) {
            case FactoryType::AIRDROPFACTORY->value:
                $query =  $factoryItem->airdrops();
                if ($search) {
                    $query->where('contract', 'LIKE', "%$search%")
                        ->orWhereHas('token', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%")
                                ->orWhere('contract', 'LIKE', "%$search%")
                                ->orWhere('symbol', 'LIKE', "%$search%");
                        });
                }
                $deployments = $query->paginate(25);
                break;
            case FactoryType::FAIRLAUNCHFACTORY->value:
            case FactoryType::PRIVATESALEFACTORY->value:
            case FactoryType::PRESALEFACTORY->value:
                $query =  $factoryItem->launchpads();
                if ($search) {
                    $query->where('contract', 'LIKE', "%$search%")
                        ->orWhereHas('token', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%")
                                ->orWhere('contract', 'LIKE', "%$search%")
                                ->orWhere('symbol', 'LIKE', "%$search%");
                        });
                }
                $deployments = $query->paginate(25);
                break;
            case FactoryType::STAKINGFACTORY->value:
            case FactoryType::STAKINGREWARDSFACTORY->value:
                $query =  $factoryItem->staking();
                if ($search) {
                    $query->where('contract', 'LIKE', "%$search%")
                        ->orWhereHas('token', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%")
                                ->orWhere('contract', 'LIKE', "%$search%")
                                ->orWhere('symbol', 'LIKE', "%$search%");
                        });
                }
                $deployments = $query->paginate(25);
                break;
            case FactoryType::GOVERNORFACTORY->value:
                $query =  $factoryItem->governors();
                if ($search) {
                    $query->where('contract', 'LIKE', "%$search%")
                        ->orWhereHas('token', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%")
                                ->orWhere('contract', 'LIKE', "%$search%")
                                ->orWhere('symbol', 'LIKE', "%$search%");
                        });
                }
                $deployments = $query->paginate(25);
                break;
            case FactoryType::TAXFACTORY->value:
            case FactoryType::STANDARDTOKENFACTORY->value:
            case FactoryType::LZFACTORY->value:
                $query =  $factoryItem->tokens();
                if ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('contract', 'LIKE', "%$search%")
                        ->orWhere('symbol', 'LIKE', "%$search%");
                }
                $deployments = $query->paginate(25);
                break;
            case FactoryType::NFTFACTORY->value:
                $query =  $factoryItem->nfts();
                if ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('contract', 'LIKE', "%$search%")
                        ->orWhere('symbol', 'LIKE', "%$search%");
                }
                $deployments = $query->paginate(25);
                break;
            case FactoryType::DEXFACTORY->value:
                $query =  $factoryItem->uniswaps();
                if ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('factory', 'LIKE', "%$search%")
                        ->orWhere('router', 'LIKE', "%$search%")
                        ->orWhere('weth9', 'LIKE', "%$search%");
                }
                $deployments = $query->paginate(25);
                break;
            case FactoryType::MIGRATOR->value:
                $query =  $factoryItem->uniswaps();
                if ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('destination', 'LIKE', "%$search%")
                        ->orWhere('source', 'LIKE', "%$search%");
                }
                $deployments = $query->paginate(25);
                break;
            default:
                $deployments = [];
                break;
        }

        return Inertia::render('Admin/Factories/Show', compact('factory', 'chainId', 'deployments'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit(Request $request, $id)
    {
        $factory = Factory::findOrFail($id);

        $factoryResource = new FactoryResource($factory);
        return Inertia::render('Admin/Factories/Edit', compact('factoryResource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'foundry' => 'required|string',
            'version' => 'required|string',
            'chainId' => 'required|string',
            'contract' => 'required|string',
            'type' => 'required|string|in:PrivateSaleFactory,AirdropFactory,Badge,Badge,GovernorFactory,Multisender,PresaleFactory,FairLaunchFactory,StakingFactory,StakingRewardsFactory,StandardTokenFactory,TokenLock,DexFactory'
        ]);

        $factory = Factory::findOrFail($id);
        $factory->foundry = $request->foundry;
        $factory->version = $request->version;
        $factory->chainId = $request->chainId;
        $factory->contract = $request->contract;
        $factory->type = $request->type;
        $factory->save();
        return back()->with('success', 'Factory updated!');
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
        Factory::destroy($id);
        return redirect()->route('admin.factories.index')->with('success', 'Factory deleted!');
    }
}
