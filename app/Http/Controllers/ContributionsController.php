<?php

namespace App\Http\Controllers;

use App\Enums\ContributionStatus;
use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Launchpad;
use App\Support\Etherscan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContributionsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, Launchpad $launchpad)
    {
        $request->validate([
            'txhash' => 'required|string'
        ]);
        if (Contribution::query()->where('txhash', $request->txhash)->exists())
            throw ValidationException::withMessages(['amount' => __('Contribution Exists')]);
        $contribution = new Contribution;
        $contribution->user_id = $request->user()->id;
        $contribution->project_id = $launchpad->project_id;
        $contribution->launchpad_id = $launchpad->id;
        $contribution->amount = 0;
        $contribution->txhash = $request->txhash;
        $contribution->status = ContributionStatus::PENDING;
        $contribution->save();
        Etherscan::verifyContribution($contribution);
        return back();
    }
}
