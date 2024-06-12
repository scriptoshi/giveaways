<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mint;
use App\Support\NftScan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MintsController extends Controller
{

    function access(Request $request)
    {
        $mints = Mint::where('user_id', $request->user()->id)
            ->where('verified', false)
            ->get();
        foreach ($mints as $mint) {
            NftScan::verifyMint($mint);
        }
        $membership = json_decode(\File::get(resource_path('js/abi/SleepfinanceMembership.json')), true);
        return Inertia::render('Mints/Membership', [
            'hasAccess' => function () use ($request, $membership) {
                if (!auth()->check()) return false;
                return  Mint::where('user_id', $request->user()->id)
                    ->where('nft_contract', $membership['addresses'][56])
                    ->where('verified', true)
                    ->exists();
            },
            'membership' => json_decode(\File::get(resource_path('js/abi/SleepfinanceMembership.json')), true),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'owner' => 'required|string',
            'nft_contract' => 'required|string',
            'tokenId' => 'required|string',
            'chainId' => 'required|numeric',
            'txhash' => 'required|string'
        ]);
        $mint = new Mint;
        $mint->user_id = $request->user()->id;
        $mint->owner = $request->owner;
        $mint->nft_contract = $request->nft_contract;
        $mint->tokenId = $request->tokenId;
        $mint->chainId = $request->chainId;
        $mint->txhash = $request->txhash;
        $mint->save();
        NftScan::verifyMint($mint);
        return back()->with('success', 'Nft token info was updated');
    }
}
