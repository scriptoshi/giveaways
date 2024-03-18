<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Chain as ChainResource;
use App\Models\Chain;
use Inertia\Inertia;

use Illuminate\Http\Request;

class ChainsController extends Controller
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
            $chainsItems  = Chain::where('name', 'LIKE', "%$keyword%")
                ->orWhere('type', 'LIKE', "%$keyword%")
                ->orWhere('chainId', 'LIKE', "%$keyword%")
                ->orWhere('explorer', 'LIKE', "%$keyword%")
                ->orWhere('label', 'LIKE', "%$keyword%")
                ->orWhere('active', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $chainsItems = Chain::with(['coins'])->latest()->paginate($perPage);
        }
        $chainList = ChainResource::collection($chainsItems);

        return Inertia::render('Admin/Chains/Index', compact('chainList'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {

        return Inertia::render('Admin/Chains/Create', compact('chains'));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string|in:api,rpc',
            'chainId' => 'required|numeric',
            'explorer' => 'required|string',
            'websockets' => 'required|array',
            'https' => 'required|array',
            'label' => 'required|string',
            'uuid' => 'required|string',
            'active' => 'required|boolean'
        ]);

        $chain = new Chain;
        $chain->name = $request->name;
        $chain->type = $request->type;
        $chain->chainId = $request->chainId;
        $chain->websockets = $request->websockets;
        $chain->https = $request->https;
        $chain->explorer = $request->explorer;
        $chain->label = $request->label;
        $chain->uuid = $request->uuid;
        $chain->active = $request->active;
        $chain->save();
        return redirect('admin.chains.index')->with('success', 'Chain added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $chain = Chain::with(['coins', 'deposits', 'withdraws'])->findOrFail($id);
        $chainResource = new ChainResource($chain);
        return Inertia::render('Admin/Chains/Show', compact('chainResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $chain = Chain::findOrFail($id);
        $chainModel = new ChainResource($chain);
        return Inertia::render('Admin/Chains/Edit', compact('chainModel'));
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
            'explorer' => 'required|string',
            'websockets' => 'required|array',
            'https' => 'required|array',
        ]);
        $chain = Chain::findOrFail($id);
        $chain->websockets = $request->websockets;
        $chain->https = $request->https;
        $chain->explorer = $request->explorer;
        $chain->save();
        return back()->with('success', 'Chain updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Chain $chain)
    {
        $chain->active = !$chain->active;
        $chain->save();
        return back()->with('success', $chain->active ? __(' :name  Enabled !', ['name' => $chain->name]) : __(' :name  Disabled !', ['name' => $chain->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Chain::destroy($id);
        return redirect('admin.chains.index')->with('success', 'Chain deleted!');
    }
}
