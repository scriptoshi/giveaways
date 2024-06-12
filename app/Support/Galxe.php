<?php

namespace App\Support;

use App\Models\Giveaway;
use App\Models\Quester;
use App\Models\User;
use Ixudra\Curl\Facades\Curl;

class Galxe
{
    /**
     * add signups to credentials
     */
    public static function signup(User $user)
    {
        $accounts = $user->accounts()->pluck('address')->all();
        $credId = config('app.galxe.signup');
        return static::updateCredentials($accounts, $credId);
    }

    /**
     * add signups to credentials
     */
    public static function giveaway(Quester $quester)
    {
        $quester->load(['user.accounts', 'giveaway']);
        $accounts = $quester->user->accounts->pluck('address')->all();
        $credId =  $quester->giveaway->galxe;
        if (!$credId) return;
        return static::updateCredentials($accounts, $credId);
    }

    public static function updateCredential(string $account, string $credId)
    {
        $apiKey = config('app.galxeApiKey');
        $query = 'mutation credentialItems {
          credentialItems(input:{
            credId:"' . $credId . '",
            operation:APPEND,
            items:["' . $account . '"]
          }){
            eligible(address:"' . $account . '")
          }
        }';
        return Curl::to('https://graphigo.prd.galaxy.eco/query')
            ->withData(['query' => $query])
            ->asJson()
            ->withHeader('access-token: ' . $apiKey)
            ->post();
    }

    public static function updateCredentials(array $accounts, string $credId)
    {
        $apiKey = config('app.galxeApiKey');
        $list = collect($accounts)->implode('","');
        $query = 'mutation credentialItems {
          credentialItems(input:{
            credId:"' . $credId . '",
            operation:APPEND,
            items:["' . $list . '"]
          }){
            eligible(address:"' . end($accounts) . '")
          }
        }';
        return Curl::to('https://graphigo.prd.galaxy.eco/query')
            ->withData(['query' => $query])
            ->asJson()
            ->withHeader('access-token: ' . $apiKey)
            ->post();
    }
}
