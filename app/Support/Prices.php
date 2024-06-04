<?php

namespace App\Support;

use App\Models\Coin;
use Curl;
use Str;
use SWeb3\Utils;

class Prices
{

    public static function moralisTokenPrices()
    {
        $moralisApiKey = config('services.moralis.apiKey');
        $coins = Coin::where('contract', '!=',  config('settings.addressZero'))
            ->whereHas('chain', fn ($q) => $q->where('testnet', false))
            ->get();
        $coins->groupBy('chainId')->map(function ($group) use ($moralisApiKey) {
            $chainId = $group->first()->chainId;
            $list = $group->map(function ($coin) {
                return ['token_address' => $coin->contract];
            })->all();
            $response = Curl::to('https://deep-index.moralis.io/api/v2.2/erc20/prices?chain=0x' .
                dechex($chainId) . '&include=percent_change')
                ->withData(['tokens' => $list])
                ->asJson()
                ->withHeader('X-API-Key: ' . $moralisApiKey)
                ->post();
            collect($response)->each(function ($item) {
                Coin::where('contract', Utils::toChecksumAddress($item->tokenAddress))
                    ->update([
                        'usd_rate' => $item->usdPrice,
                    ]);
            });
        });
    }

    public static function coinlayerPrices()
    {
        $usd = ['DAI', 'USDC', 'USDC', 'USDT', 'USDP', 'FRAX', 'BUSD'];
        Coin::whereIn('symbol', $usd)->update([
            'usd_rate' => 1
        ]);
        $apiKey = config('services.coinlayer.api_key');
        $prices = Curl::to('https://api.coinlayer.com/live')
            ->withData(['access_key' => $apiKey,])
            ->asJson()
            ->get();
        if ($prices->success) {
            $symbols = Coin::pluck('symbol')->all();
            foreach ($prices->rates as $symbol => $rate) {
                if (!in_array($symbol, $symbols)) continue;
                Coin::where('symbol', $symbol)->update([
                    'usd_rate' => $rate
                ]);
            }
        }
    }

    public static function ankrChainIdToSymbol(int $chainId): string|null
    {
        return match ($chainId) {
            1 => 'eth',
            56 => 'bsc',
            137 => 'polygon',
            43114 => 'avalanche',
            100 => 'gnosis',
            10 => 'optimism',
            42161 => 'arbitrum',
            8453 => 'base',
            250 => 'fantom',
            14 => 'flare',
            100 => 'gnosis',
            59144 => 'linea',
            1101 =>  'polygon_zkevm',
            570 => 'rollux',
            534352 => 'scroll',
            57 =>  'syscoin',
            43113 =>  'avalanche_fuji',
            default => null
        };
    }

    public static function ankrPrices()
    {
        $apiKey = config('services.ankr.apiKey');
        $url =  "https://rpc.ankr.com/multichain/{$apiKey}";
        $coins = Coin::where('symbol', '!=', 'ETH')
            ->whereHas('chain', fn ($q) => $q->where('testnet', false))
            ->get();
        $coins->each(function ($token) use ($url) {
            $symbol = self::ankrChainIdToSymbol($token->chainId);
            if (!$symbol) return;
            $response = Curl::to($url)
                ->withData([
                    'id' => Str::uuid()->getInteger(),
                    'jsonrpc' => '2.0',
                    'method' => 'ankr_getTokenPrice',
                    'params' => [
                        'blockchain' => $symbol,
                        ...($token->contract != config('settings.addressZero')
                            ? ['contractAddress' => $token->contract]
                            : [])
                    ]
                ])
                ->asJson()
                ->post();
            $price = $response?->result?->usdPrice;
            if (!$price) return;
            $token->update(['usd_rate' => $price]);
        });
    }
}
