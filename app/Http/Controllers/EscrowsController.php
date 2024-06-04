<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Escrow as EscrowResource ;
use App\Models\Escrow;
use Inertia\Inertia;

use App\Models\Ad;
use App\Models\User;
use App\Models\Coin;
use Illuminate\Http\Request;

class EscrowsController extends Controller
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
            $escrowsItems  = Escrow::with(['user','ad','coin'])->where('ad_id', 'LIKE', "%$keyword%")
                ->orWhere('user_id', 'LIKE', "%$keyword%")
                ->orWhere('coin_id', 'LIKE', "%$keyword%")
                ->orWhere('contract', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $escrowsItems = Escrow::with(['user','ad','coin'])->latest()->paginate($perPage);
        }
        $escrows = EscrowResource::collection($escrowsItems);
		$ads = Ad::pluck('id','name')->all();
		$users = User::pluck('id','name')->all();
		$coins = Coin::pluck('id','name')->all();

        return Inertia::render('Admin/Escrows/Index', compact('escrows','ads','users','coins'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
		$ads = Ad::pluck('id','name')->all();
		$users = User::pluck('id','name')->all();
		$coins = Coin::pluck('id','name')->all();

        return Inertia::render('Admin/Escrows/Create', compact('escrows','ads','users','coins'));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'ad_id' => 'required|integer|exists:ads,id',
			'user_id' => 'required|integer|exists:users,id',
			'coin_id' => 'required|integer|exists:coins,id',
			'contract' => 'required|string',
			'amount' => 'required|string'
		]);
        
        $escrow = new Escrow;
		$escrow->ad_id = $request->ad_id;
		$escrow->user_id = $request->user_id;
		$escrow->coin_id = $request->coin_id;
		$escrow->contract = $request->contract;
		$escrow->amount = $request->amount;
		$escrow->status = $request->status;
		$escrow->save();
        return redirect()->route('bets.escrows.index')->with('success', 'Escrow added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request , $id)
    {
        $escrow = Escrow::with(['user','ad','coin'])->findOrFail($id);
        $escrowResource = new EscrowResource($escrow);
        return Inertia::render('Admin/Escrows/Show', compact('escrowResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request , $id)
    {
        $escrow = Escrow::findOrFail($id);
		$ads = Ad::pluck('id','name')->all();
		$users = User::pluck('id','name')->all();
		$coins = Coin::pluck('id','name')->all();

        $escrowResource = new EscrowResource($escrow);
        return Inertia::render('Admin/Escrows/Edit', compact('escrowResource'));
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
			'ad_id' => 'required|integer|exists:ads,id',
			'user_id' => 'required|integer|exists:users,id',
			'coin_id' => 'required|integer|exists:coins,id',
			'contract' => 'required|string',
			'amount' => 'required|string'
		]);
        
        $escrow = Escrow::findOrFail($id);
		$escrow->ad_id = $request->ad_id;
		$escrow->user_id = $request->user_id;
		$escrow->coin_id = $request->coin_id;
		$escrow->contract = $request->contract;
		$escrow->amount = $request->amount;
		$escrow->status = $request->status;
		$escrow->save();
        return back()->with('success', 'Escrow updated!');
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
        Escrow::destroy($id);
        return redirect()->route('bets.escrows.index')->with('success', 'Escrow deleted!');
    }
}
