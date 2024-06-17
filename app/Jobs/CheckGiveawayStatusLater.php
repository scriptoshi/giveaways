<?php

namespace App\Jobs;

use App\Models\Giveaway;
use App\Support\Etherscan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckGiveawayStatusLater implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Giveaway $giveaway)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Etherscan::updateGiveAwayStatus($this->giveaway);
    }
}
