<?php

namespace App\Actions\Task;

use App\Enums\ConnectionProvider;
use App\Models\Connection;
use App\Models\Task;
use App\Support\Twitter as SupportTwitter;

class Twitter extends Verifier
{
    public function verify(Task $task): bool
    {
        return true;
    }

    public function validateFollow(Task $task): bool
    {
        $connection = Connection::query()
            ->where('user_id', $task->user_id)
            ->where('provider', ConnectionProvider::TWITTER)
            ->first();
        if (!$connection) return false;
        $username =   str(str($task->quest->username)->explode('/')->last())->explode('?')->first();
        $quester = new SupportTwitter($connection);
        return $quester->followed($username);
    }

    public function validateLikeAndRetweet(Task $task): bool
    {
        $connection = Connection::query()
            ->where('user_id', $task->user_id)
            ->where('provider', ConnectionProvider::TWITTER)
            ->first();
        if (!$connection) return false;
        $username =   str(str($task->quest->username)->explode('/')->last())->explode('?')->first();
        $quester = new SupportTwitter($connection);
        return $quester->followed($username);
    }
    
    public function validateComment(Task $task): bool
    {
        $connection = Connection::query()
            ->where('user_id', $task->user_id)
            ->where('provider', ConnectionProvider::TWITTER)
            ->first();
        if (!$connection) return false;
        $username =   str(str($task->quest->username)->explode('/')->last())->explode('?')->first();
        $quester = new SupportTwitter($connection);
        return $quester->followed($username);
    }
}
