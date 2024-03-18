<?php

namespace App\Console\Commands;

use App\Support\Nftscan;
use Illuminate\Console\Command;

class NftscanUpdateMints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nftscan:mints';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Mints via the NFT scan API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Nftscan::updateMints();
        return Command::SUCCESS;
    }
}
