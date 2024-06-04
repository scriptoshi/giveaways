<?php

namespace App\Support;

use App\Enums\ContributionStatus;
use App\Enums\GiveawayStatus;
use App\Enums\PackageType;
use App\Enums\QuestType;
use App\Enums\TaskType;
use App\Enums\TicketStatus;
use App\Models\Coin;
use App\Models\Contribution;
use App\Models\Giveaway;
use App\Models\Launchpad;
use App\Models\Mint;
use App\Models\Package;
use App\Models\Ticket;
use App\Models\User;
use App\Web3\Utils as Web3Utils;
use Cache;
use Carbon\Carbon;
use Curl;
use Illuminate\Database\Eloquent\Builder;
use SWeb3\Utils;

class Etherscan
{

    public  static $blocks = [
        1 => 18787893,
        5 => 10482936,
        11155111 => 5813476,
        43114 => 11706487,
        43113 => 0,
        56 => 34361207,
        97 => 35963435,
        137 => 51139900,
        80001 => 0,
        42161 => 160179138,
        42170 => 34215692,
        10 => 0,
        420 => 0,
        100 => 0,
        1284 => 0,
        1285 => 0,
        1287 => 0,
        288 => 0,
        2888 => 0,
        42220 => 0,
        25 => 0,
        250 => 0,
        250 => 0,
        250 => 7905200,
    ];

    public static $urls = [
        1 => 'https://api.etherscan.io/api',
        5 => 'https://api-sepolia.etherscan.io/api',
        11155111 => 'https://api-sepolia.etherscan.io/api',
        43114 => 'https://api.snowtrace.io/api',
        43113 => 'https://api-testnet.snowtrace.io/api',
        56 => 'https://api.bscscan.com/api',
        97 => 'https://api-testnet.bscscan.com/api',
        137 => 'https://api.polygonscan.com/api',
        80001 => 'https://api-testnet.polygonscan.com/api',
        42161 => 'https://api.arbiscan.io/api',
        42170 => 'https://nova-api.arbiscan.io/api',
        10 => 'https://api-optimistic.etherscan.io/api',
        420 => 'https://api-sepolia-optimistic.etherscan.io/api',
        100 => 'https://api.gnosisscan.io/api',
        1284 => 'https://api-moonbeam.moonscan.io/api',
        1285 => 'https://api-moonriver.moonscan.io/api',
        1287 => 'https://api-moonbase.moonscan.io/api',
        288 => 'https://api.bobascan.com/api',
        2888 => 'https://api-testnet.bobascan.com/api',
        42220 => 'https://api.celoscan.io/api',
        25 => 'https://api.cronoscan.com/api',
        250 => 'https://api.ftmscan.com/api',
        250 => 'https://api.ftmscan.com/api',
        250 => 'https://api.basescan.org/api',
    ];

    public static $keys = [
        1 => '95F2RS5XNA1HSBYCCSZWPG264CNWSMIA6J',
        5 => '95F2RS5XNA1HSBYCCSZWPG264CNWSMIA6J',
        11155111 => '95F2RS5XNA1HSBYCCSZWPG264CNWSMIA6J',
        43114 => 'GCNY1K3VXM77KFWZ9XN43TUHDFAH8BBP8U',
        43113 => 'GCNY1K3VXM77KFWZ9XN43TUHDFAH8BBP8U',
        56 => 'ECBTE8IXXWKBTFPNM2QRDYUAUAWCDBGE2V',
        97 => 'ECBTE8IXXWKBTFPNM2QRDYUAUAWCDBGE2V',
        137 => 'XH9QHWCTZFRYMSSE62KNFVPPYSP2137TM5',
        80001 => 'XH9QHWCTZFRYMSSE62KNFVPPYSP2137TM5',
        42161 => 'CNSIJTE66AXAKPB2SFRJA8GNU4SJUDRSIN',
        42170 => 'IHXZ26BSAPC2S4VCEQDYCSBB6D196BGVE8',
        10 => 'WU61QQB9DF2R2PFH6YTHU8KRZIEU6ER4YU',
        420 => 'WU61QQB9DF2R2PFH6YTHU8KRZIEU6ER4YU',
        100 => 'VZCXGKWHDBWEHT3UDPMT8RBJPXTRHXIJKS',
        1284 => 'E7DN8V5KCHERMH4PNNAZRX4UD1Z3Z589AX',
        1285 => '7ZQGVI1HCDZZGFWTJSPGK2EUD6SFJKVFQH',
        1287 => '7ZQGVI1HCDZZGFWTJSPGK2EUD6SFJKVFQH',
        288 => 'B4KFBZGNAE9513B6FERU64CF1EYVXTBNMA',
        2888 => 'B4KFBZGNAE9513B6FERU64CF1EYVXTBNMA',
        42220 => 'NXXJ989KK2DV662FWVQFIY3H2H7UXJTK25',
        44787 => 'NXXJ989KK2DV662FWVQFIY3H2H7UXJTK25',
        25 => 'U8169Q7P4NP4EZCTK27V1MMVUTW5FA86Q7',
        338 => 'U8169Q7P4NP4EZCTK27V1MMVUTW5FA86Q7',
        250 => 'FFD1CW3H6D2CN796TAJXCE828262H8AH9Z',
        4002 => 'FFD1CW3H6D2CN796TAJXCE828262H8AH9Z',
        8453 => 'RRBYC1AW4N1X6WI9M2ASSV1NH4WI4BUCBW',
    ];
    public static function getBlock($chainId, string $for): object
    {
        $key = static::$keys[$chainId];
        if (!$key) return null;
        $url = static::$urls[$chainId];
        if (!$url) return null;
        $blockData = [
            'module' => 'proxy',
            'action' => 'eth_blockNumber',
            'apikey' => $key,
        ];
        $blockNumber = Curl::to($url)->withData($blockData)->asJsonResponse()->get();
        $block = hexdec($blockNumber->result);
        $lastBlock = Cache::pull("blockNumber-{$for}-{$chainId}", $block - 100);
        Cache::forever("blockNumber-{$for}-{$chainId}", $block);
        return (object) [
            'latestBlock' => $block,
            'lastBlock' =>   $lastBlock ?? $block - 100,
        ];
    }

    public static function getTransactionByHash($chainId, string $hash): object
    {
        $key = static::$keys[$chainId];
        if (!$key) return null;
        $url = static::$urls[$chainId];
        if (!$url) return null;
        $blockData = [
            'module' => 'proxy',
            'action' => 'eth_getTransactionByHash',
            'txhash' => $hash,
            'apikey' => $key,
        ];
        $response = Curl::to($url)->withData($blockData)->asJsonResponse()->get();
        return $response->result;
    }


    public static function updateGiveAwayStatus(Giveaway $giveaway)
    {
        $prizeClaim = json_decode(\File::get(resource_path('js/abi/PrizeClaim.json')), true);
        $usdt = [
            11155111 => '0x6475e543a0EF140cD407b9385a8C09c29A5813f9',
            56 => '0x55d398326f99059fF775485246999027B3197955',
        ];
        $claimAddress = $prizeClaim['addresses'][$giveaway->chainId];
        $tx = static::getTokenTransfer($giveaway->chainId, $usdt[(int) $giveaway->chainId], $giveaway->hash, $giveaway->account);
        if (isset($tx->to)) { // if tx is ready save everyone pain.
            if (Utils::toChecksumAddress($tx->to) != Utils::toChecksumAddress($claimAddress)) {
                $giveaway->error = 'Invalid transaction hash';
                $giveaway->status = GiveawayStatus::ERROR;
                $giveaway->save();
                return;
            }
            $sleepPrice = 1000;
            $amount = Web3Utils::toBTC($tx->value, $tx->tokenDecimal);
            $giveaway->prize = $amount / ($giveaway->num_winners * 2);
            $giveaway->fee =  $amount / 2;
            $giveaway->sleep = $giveaway->fee * $sleepPrice;
            $giveaway->sleep_balance = $giveaway->sleep;
            $giveaway->status = GiveawayStatus::PAID;
            $giveaway->save();
        }
    }

    public static function getTokenBalance($chainId, $contract, $address): string
    {
        $key = static::$keys[$chainId];
        if (!$key) return null;
        $url = static::$urls[$chainId];
        if (!$url) return null;
        $callData = [
            'module' => 'account',
            'action' => 'tokenbalance',
            'contractaddress' => $contract,
            'address' => $address,
            'tag' => 'latest',
            'apikey' => $key,
        ];
        $response = Curl::to($url)->withData($callData)->asJsonResponse()->get();
        return $response->result;
    }

    public static function getTokenTransfer($chainId, $contract, string $hash, $from): object|null
    {
        $key = static::$keys[$chainId];
        if (!$key) return null;
        $url = static::$urls[$chainId];
        if (!$url) return null;
        $block = static::getBlock($chainId, 'verifyTokenTransfer');
        $blockData = [
            'module' => 'account',
            'action' => 'tokentx',
            'contractaddress' => $contract,
            'address' => $from,
            'page' => 1,
            'offset' => 10,
            'startblock' => $block->latestBlock - 1000,
            'endblock' => $block->latestBlock,
            'sort' => 'asc',
            'apikey' => $key,
        ];
        $response = Curl::to($url)->withData($blockData)->asJsonResponse()->get();
        $tx = collect($response->result)->first(fn ($res) =>  strtolower($res->hash) ==  strtolower($hash));
        if (!$tx) return null;
        return $tx;
    }

    public static function loadContributions(Launchpad $launchpad)
    {
        $key = static::$keys[$launchpad->chainId];
        if (!$key) return null;
        $url = static::$urls[$launchpad->chainId];
        if (!$url) return null;
        $block = static::getBlock($launchpad->chainId, 'verifyTokenTransfer');
        $callData = [
            'module' => 'account',
            'action' => 'txlist',
            'address' => $launchpad->contract,
            'startblock' => $launchpad->lastblock ?? ($block->latestBlock - 1000),
            'endblock' => $block->latestBlock,
            'sort' => 'asc',
            'apikey' => $key,
        ];
        $response = Curl::to($url)->withData($callData)->asJsonResponse()->get();
        $done = $launchpad->contributions()->pluck('txhash')->all();
        dump($response);
        foreach ($response->result as $tx) {
            if (in_array($tx->hash, $done)) continue;
            $amount = Web3Utils::toBTC($tx->value, 18);
            if ($amount == 0) continue;
            $user = User::query()->whereHas('account', function (Builder $query) use ($tx) {
                $query->where('address', Utils::toChecksumAddress($tx->from));
            })->first();
            if (!$user) continue;
            $coin = Coin::query()->where('chainId', $launchpad->chainId)->where('is_native', true)->first();
            $contribution = new Contribution;
            $contribution->user_id = $user->id;
            $contribution->project_id = $launchpad->project_id;
            $contribution->launchpad_id = $launchpad->id;
            $contribution->txhash = $tx->hash;
            $contribution->amount = $amount;
            $contribution->amount_usd = $amount * $coin->usd_rate;
            $contribution->status = ContributionStatus::VERIFIED;
            $contribution->save();
        }
        $launchpad->lastblock = $block->latestBlock;
        $launchpad->save();
    }

    public static function verifyContribution(Contribution $contribution)
    {
        $contribution->load('launchpad.project');
        $tx = static::getTransactionByHash((int)$contribution->launchpad->chainId, $contribution->txhash);
        if (!$tx) return;
        $amount = Web3Utils::toBTC(hexdec($tx->value), 18);
        if ($amount == 0) {
            $contribution->status = ContributionStatus::ZERO;
            $contribution->save();
            return;
        }
        $user = User::query()->whereHas('account', function (Builder $query) use ($tx) {
            $query->where('address', Utils::toChecksumAddress($tx->from));
        })->first();
        if (!$user || $contribution->user_id != $user?->id) {
            $contribution->status = ContributionStatus::MISMATCH;
            $contribution->save();
            return;
        }
        $contribution->status = ContributionStatus::VERIFIED;
        $coin = Coin::query()->where('chainId', $contribution->launchpad->chainId)->where('is_native', true)->first();
        //$contribution->address = Utils::toChecksumAddress($tx->from);
        $contribution->amount = $amount;
        $contribution->amount_usd = $amount * $coin->usd_rate;
        $contribution->save();
        return;
    }



    public static function gas($chainId)
    {
        $key = static::$keys[$chainId];
        $url = static::$urls[$chainId];
        if (!$key || !$url) return [];
        $gasData = [
            'module' => 'gastracker',
            'action' => 'gasoracle',
            'apikey' => $key,
        ];
        return Curl::to($url)->withData($gasData)->asJsonResponse()->get();
    }
}
