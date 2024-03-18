<?php

namespace App\Http\Controllers;

use App\Actions\Upload;
use App\Enums\FactoryType;
use App\Enums\TokenType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Amm as ResourcesAmm;
use App\Http\Resources\Badge as ResourcesBadge;
use App\Http\Resources\Factory as ResourcesFactory;
use App\Http\Resources\Token as TokenResource;
use App\Models\Amm;
use App\Models\Badge;
use App\Models\Chain;
use App\Models\Factory;
use App\Models\Token;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TokensController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $order = $request->get('order', 'latest');
        $query = Token::query()
            ->where('user_id', $request->user()->id)
            ->with(['chain', 'whitelist']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('website', 'LIKE', "%$keyword%");
        }
        if (!empty($order)) {
            switch ($order) {
                case 'latest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
            }
        }
        $tokensItems = $query->latest()->paginate(25);
        $tokens = TokenResource::collection($tokensItems);
        return Inertia::render('Tokens/Index', compact('tokens'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $chainId, TokenType $type)
    {
        $factoryModel = Factory::where('chainId', $chainId)
            ->where('type', $type->getFactory())
            ->latest()
            ->first();

        $factory = $factoryModel ? new ResourcesFactory($factoryModel) : null;
        $amms = ResourcesAmm::collection(Amm::all())->groupBy('chainId');
        return Inertia::render('Tokens/Create', compact('amms', 'factory', 'type'));
    }



    /**
     * Store a newly created resource in storage axios.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $projectHash = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'factory_id' => 'required_if:external,false|integer|exists:factories,id',
            'logo_uri' => 'string|required',
            'symbol' => 'required|string',
            'contract' => 'nullable|string',
            'decimals' => 'required|numeric',
            'total_supply' => 'required|numeric',
            'description' => 'nullable|string',
            'token' => 'required|string',
            'chainId' => 'required|numeric',
            'txhash' => 'nullable|string',
            'redirect' => 'nullable|string'
        ]);
        $factory = Factory::find($request->factory_id);
        $token = Token::where('chainId', $request->chainId)
            ->where('contract', $request->contract)->first();
        if ($token) {
            if ($request->wantsJson()) {
                return new TokenResource($token);
            }
            if ($request->filled('redirect'))
                return Inertia::location($request->redirect);
            return redirect()->route('tokens.project',  $token->contract);
        }
        $token = new Token;
        $chain = Chain::where('chainId', $request->chainId)->firstOrFail();
        $token->user_id = $request->user()->id;
        $token->factory_id = $factory->id;
        $token->chain_id = $chain->id;
        $token->chainId = $chain->chainId;
        $token->name = $request->name;
        $token->contract = $request->token;
        $token->symbol = $request->symbol;
        $token->decimals = $request->decimals;
        $token->total_supply = $request->total_supply;
        $token->contract_name = $request->contract;
        $token->logo_uri = $request->upload_logo
            ? app(Upload::class)->upload($request->logo_uri)
            : $request->logo_uri;
        $token->txhash = $request->txhash;
        $token->type = match ($factory->type) {
            FactoryType::TAXFACTORY => TokenType::TAXTOKEN,
            FactoryType::LZFACTORY => TokenType::CROSSCHAIN,
            default => TokenType::STANDARD
        };
        $token->save();
        if ($request->wantsJson()) {
            return new TokenResource($token);
        }
        if ($request->filled('redirect')) return Inertia::location($request->redirect);
        return  redirect()->route('tokens.index');
    }

    /**
     * Store a newly created resource in storage axios.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Token $token)
    {
        $this->authorize('update', $token);
        $request->validate(['upload' => 'string|required|max:255']);
        $token->logo_uri =  app(Upload::class)->upload($request);
        $token->save();
        if ($request->wantsJson()) {
            return new TokenResource($token);
        }
        return  back()->with('success', __('Logo Updated'));
    }

    /**
     * Check if crosschain token is deployed
     */
    public static function checkCrosschain(Request $request)
    {
        $request->validate([
            'chainId' => "required|numeric|exists:chains,chainId",
            'token' => "string|required"
        ]);
        $isValid = Token::where('chainId', $request->chainId)
            ->where('contract', $request->token)->exists();
        return compact('isValid');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $chainId, $contract)
    {
        $badges = ResourcesBadge::collection(Badge::all());
        $tokenItem = Token::with(['factory', 'whitelist'])
            ->where('chainId', $chainId)
            ->where('contract', $contract)->firstOrFail();
        $this->authorize('view', $tokenItem);
        $token = new TokenResource($tokenItem);
        return Inertia::render('Tokens/Show', compact('token', 'chainId'));
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function crosschain(Request $request, $chainId, $contract)
    {
        $tokenItem = Token::with(['factory'])
            ->where('chainId', $chainId)
            ->where('contract', $contract)->firstOrFail();
        $token = new TokenResource($tokenItem);
        return Inertia::render('Tokens/Crosschain', compact('token'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Token $token)
    {
        $this->authorize('update', $token);
        $token->status = !$token->status;
        $token->save();
        return back()->with('success', $token->status ? __(' :name Token Enabled!', ['name' => $token->name]) : __(' :name Token Disabled!', ['name' => $token->name]));
    }


    public function addWhiteList(Request $request, Token $token)
    {
        $this->authorize('update', $token);
        $request->validate(['list' => 'array']);
        foreach ($request->list as $address) {
            $token->whitelist()->updateOrCreate([
                'address' => $address,
            ], ['address' => $address]);
        }
        return back()->with('success', __('Whitelist added successfully'));
    }

    public function removeWhiteList(Request $request, Token $token)
    {
        $this->authorize('update', $token);
        $request->validate(['list' => 'array']);
        foreach ($request->list as $address) {
            $token->whitelist()->where('address', $address)->delete();
        }
        return back()->with('success', __('Whitelist removed successfully'));
    }

    public function updateLogo(Request $request, Token $token)
    {

        $this->authorize('update', $token);
        $request->validate(['logo_uri' => 'required|string']);
        if (config('app.uploads_disk') === 'public') {
            $token->logo_uri = app(Upload::class)->upload($request->logo_uri);
        } else {
            $token->logo_uri = $request->logo_uri;
        }
        $token->save();
        return back()->with('success', 'Token Logo Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Token $token)
    {
        $this->authorize('delete', $token);
        $token->delete();
        return back()->with('success', 'Token delisted');
    }
}
