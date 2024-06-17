<?php

namespace App\Jobs;

use App\Models\Giveaway;
use App\Models\Topup;
use App\Support\Etherscan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckTopupStatusLater implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Topup $topup)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Etherscan::updateTopupStatus($this->topup);
    }
}
