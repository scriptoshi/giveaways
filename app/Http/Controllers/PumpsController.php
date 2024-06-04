<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Pump as PumpResource ;
use App\Models\Pump;
use Inertia\Inertia;

use App\Models\Quester;
use Illuminate\Http\Request;

class PumpsController extends Controller
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
            $pumpsItems  = Pump::with(['quester'])->where('quester_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $pumpsItems = Pump::with(['quester'])->latest()->paginate($perPage);
        }
        $pumps = PumpResource::collection($pumpsItems);
		$questers = Quester::pluck('id','name')->all();

        return Inertia::render('Admin/Pumps/Index', compact('pumps','questers'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
		$questers = Quester::pluck('id','name')->all();

        return Inertia::render('Admin/Pumps/Create', compact('pumps','questers'));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'quester_id' => 'required|integer|exists:questers,id'
		]);
        
        $pump = new Pump;
		$pump->quester_id = $request->quester_id;
		$pump->save();
        return redirect()->route('bets.pumps.index')->with('success', 'Pump added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request , $id)
    {
        $pump = Pump::with(['quester'])->findOrFail($id);
        $pumpResource = new PumpResource($pump);
        return Inertia::render('Admin/Pumps/Show', compact('pumpResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request , $id)
    {
        $pump = Pump::findOrFail($id);
		$questers = Quester::pluck('id','name')->all();

        $pumpResource = new PumpResource($pump);
        return Inertia::render('Admin/Pumps/Edit', compact('pumpResource'));
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
			'quester_id' => 'required|integer|exists:questers,id'
		]);
        
        $pump = Pump::findOrFail($id);
		$pump->quester_id = $request->quester_id;
		$pump->save();
        return back()->with('success', 'Pump updated!');
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
        Pump::destroy($id);
        return redirect()->route('bets.pumps.index')->with('success', 'Pump deleted!');
    }
}
