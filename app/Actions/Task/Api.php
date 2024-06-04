<?php

namespace App\Actions\Task;

use App\Models\Account;
use App\Models\Task;
use Ixudra\Curl\Facades\Curl;

class Api extends Verifier
{
    public function verify(Task $task): bool
    {
        $quest = $task->quest;
        $addresses = Account::where('user_id', $task->user_id)->pluck('address')->implode(',');
        $url = $quest->username;
        $apiKey = $quest->groupId;
        $response = Curl::to($url)
            ->asJson()
            ->withData([
                'addresses' => $addresses,
                'key' => $apiKey,
            ])->get();
        if ($response?->complete) return true;
        return false;
    }
}
