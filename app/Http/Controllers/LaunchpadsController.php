<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Launchpad as LaunchpadResource;
use App\Models\Launchpad;
use Inertia\Inertia;

use App\Models\Project;
use App\Support\Etherscan;
use File;
use Illuminate\Http\Request;

class LaunchpadsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        if (!empty($keyword)) {
            $launchpadsItems  = Launchpad::with(['project', 'uploads', 'logo'])->where('project_id', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('symbol', 'LIKE', "%$keyword%")
                ->orWhere('chainId', 'LIKE', "%$keyword%")
                ->orWhere('address', 'LIKE', "%$keyword%")
                ->orWhere('contract', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $launchpadsItems = Launchpad::with(['project', 'uploads', 'logo'])->latest()->paginate($perPage);
        }
        $launchpads = LaunchpadResource::collection($launchpadsItems);
        $projects = Project::pluck('id', 'name')->all();
        return Inertia::render('Admin/Launchpads/Index', compact('launchpads', 'projects'));
    }


    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'name' => 'required|string',
            'symbol' => 'required|string',
            'chainId' => 'required|numeric',
            'address' => 'required|string',
            'contract' => 'required|string'
        ]);
        $block = Etherscan::getBlock($request->chainId, 'create-launchpad');
        $abi = json_decode(File::get(resource_path('js/abi/Fairlaunch.json')));
        $launchpad = new Launchpad;
        $launchpad->project_id = $project->id;
        $launchpad->name = $request->name;
        $launchpad->symbol = $request->symbol;
        $launchpad->chainId = $request->chainId;
        $launchpad->address = $request->address;
        $launchpad->contract = $request->contract;
        $launchpad->abi = $abi->abi;
        $launchpad->lastblock  = $block->latestBlock;
        $launchpad->save();
        return back();
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $launchpad = Launchpad::with(['project', 'uploads', 'logo'])->findOrFail($id);
        $launchpadResource = new LaunchpadResource($launchpad);
        return Inertia::render('Admin/Launchpads/Show', compact('launchpadResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $launchpad = Launchpad::findOrFail($id);
        $projects = Project::pluck('id', 'name')->all();

        $launchpadResource = new LaunchpadResource($launchpad);
        return Inertia::render('Admin/Launchpads/Edit', compact('launchpadResource'));
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
            'project_id' => 'required|integer|exists:users,id',
            'name' => 'required|string',
            'symbol' => 'required|string',
            'chainId' => 'required|string',
            'address' => 'required|string',
            'contract' => 'required|string'
        ]);

        $launchpad = Launchpad::findOrFail($id);
        $launchpad->project_id = $request->project_id;
        $launchpad->name = $request->name;
        $launchpad->symbol = $request->symbol;
        $launchpad->chainId = $request->chainId;
        $launchpad->address = $request->address;
        $launchpad->contract = $request->contract;
        $launchpad->save();
        return back()->with('success', 'Launchpad updated!');
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
        Launchpad::destroy($id);
        return redirect()->route('bets.launchpads.index')->with('success', 'Launchpad deleted!');
    }
}
