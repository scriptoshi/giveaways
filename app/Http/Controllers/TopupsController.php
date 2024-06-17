<?php

namespace App\Http\Controllers;

use App\Enums\GiveawayStatus;
use App\Http\Controllers\Controller;
use App\Jobs\CheckTopupStatusLater;
use App\Models\Topup;

use App\Models\Giveaway;
use App\Support\Etherscan;
use Illuminate\Http\Request;


class TopupsController extends Controller
{


    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, Giveaway $giveaway)
    {
        $request->validate([
            'hash' => 'nullable|string',
            'num_winners' => 'required|numeric',
            'account' => 'required',
        ]);
        if (!$request->user()->isAdmin() && $request->chainId != 56) {
            return back()->with('error', 'Invalid transaction hash. Only BNB supported');
        }
        $topup = new Topup;
        $topup->giveaway_id = $giveaway->id;
        $topup->paid = 0;
        $topup->account = $request->account;
        $topup->hash = $request->hash;
        $topup->num_winners = $request->num_winners;
        $topup->status = GiveawayStatus::UNPAID;
        $topup->save();
        $checked = Etherscan::updateTopupStatus($topup);
        if (!$checked) {
            // check in one minute
            CheckTopupStatusLater::dispatch($topup)
                ->delay(now()->addMinute());
        }
        return back();
    }
}
