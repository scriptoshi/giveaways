<?php

namespace App\Actions\Task;

use App\Models\Account;
use App\Models\Task;
use App\Support\NftScan;


class Nft extends Verifier
{
    public function verify(Task $task): bool
    {
        $quest = $task->quest;
        $addresses = Account::where('user_id', $task->user_id)->pluck('address')->all();
        $contract = $quest->username;
        $chainId = $quest->data['chainId'];
        foreach ($addresses as $address) {
            if (NftScan::hasNft($contract, $address, $chainId)) return true;
        }
        return false;
    }
}
