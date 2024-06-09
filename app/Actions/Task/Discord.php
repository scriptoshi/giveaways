<?php

namespace App\Actions\Task;

use App\Enums\ConnectionProvider;
use App\Models\Connection;
use App\Models\Task;
use App\Support\Discord as DiscordSupport;

class Discord extends Verifier
{
    public function verify(Task $task): bool
    {
        $connection = Connection::query()
            ->where('user_id', $task->user_id)
            ->where('provider', ConnectionProvider::DISCORD)
            ->first();
        if (!$connection) return false;
        $inviteId =  str(str($task->quest->username)->explode('/')->last())->explode('?')->first();
        return DiscordSupport::userJoinedByInvite($inviteId, $connection->userId);
    }
}
