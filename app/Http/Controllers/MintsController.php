<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mint as MintResource ;
use App\Models\Mint;
use Inertia\Inertia;

use App\Models\Nfts;
use Illuminate\Http\Request;

class MintsController extends Controller
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
            $mintsItems  = Mint::with(['nft'])->where('nfts_id', 'LIKE', "%$keyword%")
                ->orWhere('owner', 'LIKE', "%$keyword%")
                ->orWhere('tokenId', 'LIKE', "%$keyword%")
                ->orWhere('txhash', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $mintsItems = Mint::with(['nft'])->latest()->paginate($perPage);
        }
        $mints = MintResource::collection($mintsItems);
		$nftss = Nfts::pluck('id','name')->all();

        return Inertia::render('Admin/Mints/Index', compact('mints','nftss'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
		$nftss = Nfts::pluck('id','name')->all();

        return Inertia::render('Admin/Mints/Create', compact('mints','nftss'));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'nfts_id' => 'required|integer|exists:nfts,id',
			'owner' => 'required|string',
			'tokenId' => 'required|string',
			'txhash' => 'required|string'
		]);
        
        $mint = new Mint;
		$mint->nfts_id = $request->nfts_id;
		$mint->owner = $request->owner;
		$mint->tokenId = $request->tokenId;
		$mint->txhash = $request->txhash;
		$mint->save();
        return redirect()->route('bets.mints.index')->with('success', 'Mint added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request , $id)
    {
        $mint = Mint::with(['nft'])->findOrFail($id);
        $mintResource = new MintResource($mint);
        return Inertia::render('Admin/Mints/Show', compact('mintResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request , $id)
    {
        $mint = Mint::findOrFail($id);
		$nftss = Nfts::pluck('id','name')->all();

        $mintResource = new MintResource($mint);
        return Inertia::render('Admin/Mints/Edit', compact('mintResource'));
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
			'nfts_id' => 'required|integer|exists:nfts,id',
			'owner' => 'required|string',
			'tokenId' => 'required|string',
			'txhash' => 'required|string'
		]);
        
        $mint = Mint::findOrFail($id);
		$mint->nfts_id = $request->nfts_id;
		$mint->owner = $request->owner;
		$mint->tokenId = $request->tokenId;
		$mint->txhash = $request->txhash;
		$mint->save();
        return back()->with('success', 'Mint updated!');
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
        Mint::destroy($id);
        return redirect()->route('bets.mints.index')->with('success', 'Mint deleted!');
    }
}
