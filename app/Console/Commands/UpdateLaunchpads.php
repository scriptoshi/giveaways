<?php

namespace App\Console\Commands;

use App\Models\Launchpad;
use App\Support\Etherscan;
use Illuminate\Console\Command;

class UpdateLaunchpads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-launchpads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $launchpads = Launchpad::where('created_at', '>=', now()->subDays(10))->get();
        $launchpads->each(function (Launchpad $launch) {
            Etherscan::loadContributions($launch);
            sleep(1);
        });
    }
}
