<?php

namespace App\Actions\Task;

use App\Enums\ConnectionProvider;
use App\Models\Connection;
use App\Models\Task;
use App\Support\Telegram as SupportTelegram;

class Telegram extends Verifier
{
    public function verify(Task $task): bool
    {
        $connection = Connection::query()
            ->where('user_id', $task->user_id)
            ->where('provider', ConnectionProvider::TELEGRAM)
            ->first();
        if (!$connection) return false;
        $groupName =  str(str($task->quest->username)->explode('/')->last())->explode('?')->first();
        return SupportTelegram::checkUserJoined($groupName, $connection->userId);
    }
}
