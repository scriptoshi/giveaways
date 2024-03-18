<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Chain;
use App\Models\Bet;
use App\Models\Market;
use App\Models\Slip;
use App\Models\Team;
use App\Models\Ticket;
use Illuminate\Http\Request;
use phpseclib\Math\BigInteger;

class WebhooksController extends Controller
{
    public function slips(Request $request)
    {
        $slipId = "0x" . (new BigInteger($request->token_id))->toHex();
        $slip = Slip::query()->where('slipId', $slipId)->first();
        if (!$slip) return;
        $slip->mint()
            ->firstOrCreate(
                [
                    'tokenId' => $request->token_id,
                ],
                [
                    'chain_id' => $slip->chain_id,
                    'chainId' => $slip->chainId,
                    'owner' => $request->receive,
                    'txhash' => $request->hash,
                ]
            );
    }

    public function tickets(Request $request)
    {
        $slipId = $request->token_id;
        $slip = Ticket::query()->where('slipId', $slipId)->first();
        if (!$slip) return;
        $slip->mint()
            ->firstOrCreate(
                [
                    'tokenId' => $request->token_id,
                ],
                [
                    'chain_id' => $slip->chain_id,
                    'chainId' => $slip->chainId,
                    'owner' => $request->receive,
                    'txhash' => $request->hash,
                ]
            );
    }

    public function markets(Request $request, $chain)
    {
        $tokenId = "0x" . (new BigInteger($request->token_id))->toHex();
        $model = null;
        $model = Market::query()
            ->where('marketId', $tokenId)
            ->first();
        if (!$model) {
            // a bet was minted
            $model = Bet::query()
                ->where('betId', $tokenId)
                ->first();
        }
        if (!$model) return;
        $chain = Chain::query()->where('slug', $chain)->first();
        $model->mints()
            ->firstOrCreate(
                [
                    'tokenId' => $request->token_id,
                    'chainId' =>  $chain->chainId,
                ],
                [
                    'chain_id' => $chain->id,
                    'owner' => $request->receive,
                    'txhash' => $request->hash,
                ]
            );
    }

    public function teams(Request $request, $chain)
    {

        $team = Team::query()
            ->where('teamId', $request->token_id)
            ->first();
        if (!$team) return;
        $chain = Chain::query()->where('slug', $chain)->first();
        $team->mints()
            ->firstOrCreate(
                [
                    'tokenId' => $request->token_id,
                    'chainId' =>  $chain->chainId,
                ],
                [
                    'chain_id' => $chain->id,
                    'owner' => $request->receive,
                    'txhash' => $request->hash,
                ]
            );
    }
}
