<?php

namespace App\Support;

use App\Enums\SlipStatus;
use App\Models\Mint;
use App\Models\Package;
use App\Models\Slip;
use Illuminate\Validation\ValidationException;
use Ixudra\Curl\Facades\Curl;

class NftScan
{
    // curl --nft GET --url 'https://bnbapi.nftscan.com/api/v2/transactions/account/0x31ca0a9d2c8c8fb50ea897d0b420c26919088d5e?event_type=Mint&sort_direction=desc'  --header 'X-API-KEY: VoBcoXonRfeawQgalKk6MuVJ'
    public  static function url($chainId)
    {
        $prefix  = match ($chainId) {
            config('chains.MAINNET') => 'restapi',
            config('chains.BINANCE') => 'bnbapi',
            config('chains.POLYGON') => 'polygonapi',
            config('chains.OPTIMISM') => 'optimismapi',
            config('chains.ZKSYNC') => 'zksyncapi',
            config('chains.LINEAR') => 'lineaapi',
            config('chains.AVALANCHE') => 'avaxapi',
            config('chains.CRONOS') => 'cronosapi',
            config('chains.FANTOM') => 'fantomapi',
            config('chains.GNOSIS') => 'gnosisapi',
            config('chains.ARBITRUM_ONE') => 'arbitrumapi',
            default => 'restapi'
        };
        return "https://$prefix.nftscan.com/api/v2";
    }
    /**
     * get the mint TX for an nft
     */
    public static function hasNft($contract, $owner, $chainId): bool
    {
        $apiKey = config('app.nftscanApiKey');
        $url = static::url($chainId) . "/account/own/$owner";
        $response = Curl::to($url)
            ->withData([
                'erc_type' => 'erc721',
                'show_attribute' => 'false',
                'contract_address' => $contract
            ])
            ->withHeader("X-API-KEY: $apiKey")
            ->asJson()
            ->get();
        return $response->data->total > 0;
    }
}
