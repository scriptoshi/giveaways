<?php

namespace App\Console\Commands;

use App\Support\Prices;
use Illuminate\Console\Command;

class coinLayerPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coinlayer:prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  'Update token prices from CoinLayer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        Prices::coinlayerPrices();
    }
}
