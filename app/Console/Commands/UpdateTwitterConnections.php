<?php

namespace App\Console\Commands;

use App\Models\Connection;
use App\Support\Twitter;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class UpdateTwitterConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-twitter-connections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update twitter followers count';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        Connection::query()->whereHas('user', function (Builder $query) {
            $query->where('last_seen_at', '>=', now()->subDays(2));
        })->get()->each(function (Connection $connection) {
            (new Twitter($connection))->me();
        });
    }
}
