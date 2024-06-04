<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Nft as NftResource ;
use App\Models\Nft;
use Inertia\Inertia;

use App\Models\Giveaway;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Http\Request;

class NftsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        if (!empty($keyword)) {
            $nftsItems  = Nft::with(['giveaway','quest','user','uploads','nft'])->where('giveaway_id', 'LIKE', "%$keyword%")
                ->orWhere('quest_id', 'LIKE', "%$keyword%")
                ->orWhere('user_id', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('symbol', 'LIKE', "%$keyword%")
                ->orWhere('contract', 'LIKE', "%$keyword%")
                ->orWhere('chainId', 'LIKE', "%$keyword%")
                ->orWhere('hash', 'LIKE', "%$keyword%")
                ->orWhere('meta', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $nftsItems = Nft::with(['giveaway','quest','user','uploads','nft'])->latest()->paginate($perPage);
        }
        $nfts = NftResource::collection($nftsItems);
		$giveaways = Giveaway::pluck('id','name')->all();
		$quests = Quest::pluck('id','name')->all();
		$users = User::pluck('id','name')->all();

        return Inertia::render('Admin/Nfts/Index', compact('nfts','giveaways','quests','users'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
		$giveaways = Giveaway::pluck('id','name')->all();
		$quests = Quest::pluck('id','name')->all();
		$users = User::pluck('id','name')->all();

        return Inertia::render('Admin/Nfts/Create', compact('nfts','giveaways','quests','users'));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'giveaway_id' => 'required|integer|exists:giveaways,id',
			'quest_id' => 'required|integer|exists:quests,id',
			'user_id' => 'required|integer|exists:users,id',
			'name' => 'required|string',
			'symbol' => 'required|string',
			'contract' => 'required|string',
			'chainId' => 'required|string',
			'hash' => 'nullable|string',
			'meta' => 'nullable|string'
		]);
        
        $nft = new Nft;
		$nft->giveaway_id = $request->giveaway_id;
		$nft->quest_id = $request->quest_id;
		$nft->user_id = $request->user_id;
		$nft->name = $request->name;
		$nft->symbol = $request->symbol;
		$nft->contract = $request->contract;
		$nft->chainId = $request->chainId;
		$nft->hash = $request->hash;
		$nft->meta = $request->meta;
		$nft->save();
        return redirect()->route('bets.nfts.index')->with('success', 'Nft added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request , $id)
    {
        $nft = Nft::with(['giveaway','quest','user','uploads','nft'])->findOrFail($id);
        $nftResource = new NftResource($nft);
        return Inertia::render('Admin/Nfts/Show', compact('nftResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request , $id)
    {
        $nft = Nft::findOrFail($id);
		$giveaways = Giveaway::pluck('id','name')->all();
		$quests = Quest::pluck('id','name')->all();
		$users = User::pluck('id','name')->all();

        $nftResource = new NftResource($nft);
        return Inertia::render('Admin/Nfts/Edit', compact('nftResource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request , $id)
    {
        $request->validate([
			'giveaway_id' => 'required|integer|exists:giveaways,id',
			'quest_id' => 'required|integer|exists:quests,id',
			'user_id' => 'required|integer|exists:users,id',
			'name' => 'required|string',
			'symbol' => 'required|string',
			'contract' => 'required|string',
			'chainId' => 'required|string',
			'hash' => 'nullable|string',
			'meta' => 'nullable|string'
		]);
        
        $nft = Nft::findOrFail($id);
		$nft->giveaway_id = $request->giveaway_id;
		$nft->quest_id = $request->quest_id;
		$nft->user_id = $request->user_id;
		$nft->name = $request->name;
		$nft->symbol = $request->symbol;
		$nft->contract = $request->contract;
		$nft->chainId = $request->chainId;
		$nft->hash = $request->hash;
		$nft->meta = $request->meta;
		$nft->save();
        return back()->with('success', 'Nft updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request , $id)
    {
        Nft::destroy($id);
        return redirect()->route('bets.nfts.index')->with('success', 'Nft deleted!');
    }
}
