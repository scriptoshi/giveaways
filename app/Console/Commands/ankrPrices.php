<?php

namespace App\Console\Commands;

use App\Support\Prices;
use Illuminate\Console\Command;

class ankrPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ankr:prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update token prices from Ankr';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        Prices::ankrPrices();
    }
}
