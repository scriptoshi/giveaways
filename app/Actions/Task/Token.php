<?php

namespace App\Actions\Task;

use App\Models\Account;
use App\Models\Task;
use App\Support\Etherscan;
use App\Web3\Utils;

class Token extends Verifier
{
    public function verify(Task $task): bool
    {
        $quest = $task->quest;
        $addresses = Account::where('user_id', $task->user_id)->pluck('address')->all();
        $contract = $quest->username;
        $chainId = $quest->data['chainId'];
        $decimals = $quest->data['decimals'];
        foreach ($addresses as $address) {
            $bal = Etherscan::getTokenBalance($chainId, $contract, $address);
            $balance = Utils::toBTC($bal, $decimals);
            if ($balance >= $quest->min) return true;
            sleep(1);
        }
        return false;
    }
}
