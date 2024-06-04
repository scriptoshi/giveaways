<?php

namespace App\Console\Commands;

use App\Models\Giveaway;
use Illuminate\Console\Command;
use App\Enums\GiveawayStatus as GiveawayStatusEnum;
use App\Support\Etherscan;

class GiveawayStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:giveaway-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment status of giveaways';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $pending = Giveaway::query()->where('status', GiveawayStatusEnum::UNPAID)->get();
        $pending->each(function (Giveaway $giveaway) {
            Etherscan::updateGiveAwayStatus($giveaway);
        });
    }
}
