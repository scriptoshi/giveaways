<?php

namespace App\Actions\Task;

use App\Enums\ContributionStatus;
use App\Models\Task;
use App\Support\Etherscan;
use Illuminate\Http\Request;

class Contribute extends Verifier
{
    public function verify(Task $task): bool
    {
        $launchpad =  $task->giveaway->project->launchpad;
        $pending = $launchpad->contributions()
            ->where('user_id', $task->user_id)
            ->where('status', ContributionStatus::PENDING)
            ->get();
        if ($pending->count()) {
            foreach ($pending as $contribution) {
                Etherscan::verifyContribution($contribution);
                sleep(1); // dont spam the api??
            }
        }
        $amount = $launchpad->contributions()->where('user_id', $task->user_id)->sum('amount');
        return $amount >= $task->quest->min;
    }
}
