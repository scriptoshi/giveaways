<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Amm as AmmResource;
use App\Models\Amm;
use Inertia\Inertia;

use App\Models\Chain;
use App\Web3\AddressValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AmmsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $chains = $request->get('chain');
        $perPage = 25;
        $query = Amm::with(['chain']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%");
            if (str($keyword)->startsWith('0x')) {
                $query->orWhere('router', 'LIKE', "%$keyword%")
                    ->orWhere('factory', 'LIKE', "%$keyword%");
            }
        }
        if (!empty($chains)) {
            $chns = str($chains)->explode(',');
            $query->whereHas('chain', function ($q) use ($chns) {
                $q->whereIn('chainId', $chns);
            });
        }
        $ammsItems = $query->latest()->paginate($perPage);
        $amms = AmmResource::collection($ammsItems);
        return Inertia::render('Admin/Amms/Index', compact('amms'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('Admin/Amms/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'string|required',
            'chain' => 'array|required',
            'chain.id' => 'numeric|required',
            'url' => 'url|required',
            'info_url' => 'url|required',
            'token_url' => 'url|required',
            'pair_url' => 'url|required',
            'router' => ['string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
            'factory' => ['string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
        ], []);
        if (strtolower($request->router) == strtolower($request->factory)) {
            throw ValidationException::withMessages(['router' => 'Cannot be same as factory', 'factory' => 'Cannot be same as router']);
        }
        $chain = Chain::where('chainId', $request->chain['id'])->first();
        if (!$chain) {
            throw ValidationException::withMessages(['chain' => 'Select a chain']);
        }
        $amm = new Amm;
        $amm->chain_id = $chain->id;
        $amm->chainId = $chain->chainId;
        $amm->name = $request->name;
        $amm->url = $request->url;
        $amm->info_url = $request->info_url;
        $amm->token_url = $request->token_url;
        $amm->pair_url = $request->pair_url;
        $amm->router = $request->router;
        $amm->factory = $request->factory;
        $amm->status = true;
        $amm->save();
        return redirect()->route('admin.amms.index')->with('success', __('Amm Exchange added updated!'));
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $amm = Amm::with(['chain'])->findOrFail($id);
        $ammResource = new AmmResource($amm);
        return Inertia::render('Admin/Amms/Show', compact('ammResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $ammItem = Amm::with(['chain'])->findOrFail($id);
        $amm = new AmmResource($ammItem);
        return Inertia::render('Admin/Amms/Edit', compact('amm'));
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
            'name' => 'string|required',
            'url' => 'url|required',
            'info_url' => 'url|required',
            'token_url' => 'url|required',
            'pair_url' => 'url|required',
            'router' => ['string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
            'factory' => ['string', function ($attribute, $value, $fail) {
                if (AddressValidator::isValid($value) == AddressValidator::ADDRESS_INVALID) {
                    $fail('The ' . $attribute . ' is invalid.');
                }
            }],
        ]);
        if (strtolower($request->router) == strtolower($request->factory)) {
            throw ValidationException::withMessages(['router' => 'Cannot be same as factory', 'factory' => 'Cannot be same as router']);
        }
        $chain = Chain::where('chainId', $request->chain['id'])->firstOrFail();
        $amm = Amm::findOrFail($id);
        $amm->chain_id = $chain->id;
        $amm->chainId = $chain->chainId;
        $amm->name = $request->name;
        $amm->url = $request->url;
        $amm->info_url = $request->info_url;
        $amm->token_url = $request->token_url;
        $amm->pair_url = $request->pair_url;
        $amm->router = $request->router;
        $amm->factory = $request->factory;
        $amm->save();
        return back()->with('success', __('Dex Updated updated!'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, $id)
    {
        $amm = Amm::findOrFail($id);
        $amm->status = !$amm->status;
        $amm->save();
        return back()->with('success', $amm->status ? __(' :name Exchange Enabled!', ['name' => $amm->name]) : __(' :name Exchange Disabled!', ['name' => $amm->name]));
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
        Amm::destroy($id);
        return redirect()->route('admin.amms.index')->with('success', 'Amm deleted successfully');
    }
}
