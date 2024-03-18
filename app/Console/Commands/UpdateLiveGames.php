<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Support\ApiFootball\Football;
use Illuminate\Console\Command;

class UpdateLiveGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-live-games';

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
        $games = Game::query()
            ->where('startTime', '<', now())
            ->where('closed', false)
            ->get()->chunk(20);
        foreach ($games as $chunk) {
            Football::updateLiveGame($chunk);
        }
    }
}
