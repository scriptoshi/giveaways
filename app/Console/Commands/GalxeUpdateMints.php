<?php

namespace App\Console\Commands;

use App\Support\Etherscan;
use Illuminate\Console\Command;

class GalxeUpdateMints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:galxemints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Galxe users credentials for minted NFTS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Etherscan::updateGalxeMints();
        return Command::SUCCESS;
    }
}
