<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Coin as CoinResource;
use App\Models\Coin;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use App\Models\Chain;
use App\Web3\AddressValidator;
use Illuminate\Http\Request;

class CoinsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Inertia\Inertia
     */
    public function index(Request $request)
    {
        // dd(Chain::all());
        $keyword = $request->get('search');
        $chains = $request->get('chain');
        $perPage = 25;
        $query = Coin::with(['chain'])
            ->whereHas('chain', fn ($q) => $q->where('active', true));
        if (!empty($keyword)) {
            if (str($keyword)->startsWith('0x')) {
                $query->orWhere('contract', 'LIKE', "%$keyword%");
            } else {
                $query->where('name', 'LIKE', "%$keyword%")
                    ->orWhere('symbol', 'LIKE', "%$keyword%")
                    ->orWhereHas('chain', function ($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    });
            }
        }
        if (!empty($chains)) {
            $chns = str($chains)->explode(',');
            $query->whereHas('chain', function ($q) use ($chns) {
                $q->whereIn('chainId', $chns);
            });
        }

        $coinsItems = $query->latest()->paginate($perPage);
        $allCoinsList = Coin::where('contract', '!=', '0x0000000000000000000000000000000000000000')
            ->whereHas('chain', fn ($q) => $q->where('active', true))
            //->where('updated_at',"<", now()->subDay())
            ->where('active', true)
            ->get();
        $allCoins = CoinResource::collection($allCoinsList)->groupBy('chainId');
        $coins = CoinResource::collection($coinsItems);
        return Inertia::render('Admin/Coins/Index', compact('coins', 'allCoins'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('Admin/Coins/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'chain' => 'required|array',
            'chain.id' => 'numeric|exists:chains,chainId',
            'name' => 'required|string',
            'logo_uri' => 'url|max:255',
            'symbol' => 'required|string',
            'contract' => ['required', 'string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
            'decimals' => 'required|numeric',
        ]);
        $chain = Chain::where('chainId', $request->chain['id'])->first();
        if (!$chain) {
            throw ValidationException::withMessages(['chain' => 'Select a chain']);
        }
        $coin = new Coin;
        $coin->chain_id = $chain->id;
        $coin->chainId = $chain->chainId;
        $coin->name = $request->name;
        $coin->logo_uri = $request->logo_uri;
        $coin->symbol = $request->symbol;
        $coin->contract = $request->contract;
        $coin->decimals = $request->decimals;
        $coin->usd_rate = 0;
        $coin->is_native = false;
        $coin->active = true;
        $coin->save();
        return redirect()->route('admin.coins.index')->with('success', __('Purchase Coin added Successfuly!'));
    }



    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $coinItem = Coin::with(['chain'])->findOrFail($id);
        $coin = new CoinResource($coinItem);
        return Inertia::render('Admin/Coins/Edit', compact('coin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'chain_id' => 'required|integer|exists:chains,id',
            'name' => 'required|string|max:255',
            'logo_uri' => 'url|max:255',
            'symbol' => 'required|string',
            'contract' => ['required', 'string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
            'decimals' => 'required|numeric'
        ]);
        $chain = Chain::where('chainId', $request->chain['id'])->first();
        if (!$chain) {
            throw ValidationException::withMessages(['chain' => 'Select a chain']);
        }
        $coin = Coin::findOrFail($id);
        $coin->chain_id = $chain->id;
        $coin->name = $request->name;
        $coin->logo_uri = $request->logo_uri;
        $coin->symbol = $request->symbol;
        $coin->contract = $request->contract;
        $coin->decimals = $request->decimals;
        $coin->usd_rate = 0;
        $coin->is_native = false;
        $coin->active = true;
        $coin->save();
        return back()->with('success', __('Purchase Coin updated!'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateMany(Request $request)
    {
        $request->validate([
            'coins' => 'required|Array',
            'coins.*.id' => 'required|integer|exists:coins,id',
            'coins.*.name' => 'required|string|max:255',
            'coins.*.symbol' => 'required|string|max:255',
            'coins.*.decimals' => 'required|numeric',
        ]);
        batch()->update(new Coin, $request->coins, 'id');
        return 'ok';
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, $id)
    {
        $coin = Coin::findOrFail($id);
        $coin->active = !$coin->active;
        $coin->save();
        return back()->with('success', $coin->active ? __(' :name  Enabled  for Purchases!', ['name' => $coin->name]) : __(' :name  Disabled for Purchases!', ['name' => $coin->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, $id)
    {
        Coin::destroy($id);
        return redirect()->route('admin.coins.index')->with('success', 'Coin deleted!');
    }
}
