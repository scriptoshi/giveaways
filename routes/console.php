<?php

use App\Enums\BetMode;
use App\Http\Resources\BetJson;
use App\Http\Resources\CoinMarket as ResourcesCoinMarket;
use App\Models\Bet;
use App\Models\Chain;
use App\Models\Coin;
use App\Models\Game;
use App\Models\League;
use App\Models\Market;
use App\Models\Result;
use App\Models\Team;
use App\Support\ApiFootball\Football;
use App\Support\CommunityNftMetadata;
use App\Support\Etherscan;
use App\Support\LangCleanup;
use Illuminate\Support\Facades\Artisan;
use kornrunner\Keccak;
use phpseclib\Math\BigInteger;
use SWeb3\ABI;
use Web3\Web3;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/


Artisan::command('lang:strap', function () {
    Artisan::call('translatable:export', ['lang' => 'en']);
    LangCleanup::cleanUp(); // remove known false positives
    Artisan::call('vue-i18n:generate');
});

Artisan::command('bets:json', function () {
    $bets = Bet::all();
    $list = BetJson::collection($bets)->toJson();
    file_put_contents(base_path('bets.json'), $list);
});
Artisan::command('odds', function () {
    Football::getBetOdds();
});

Artisan::command('bnb:json', function () {
    $cms = Market::query()
        ->with('bets')
        ->get();
    $cm = ResourcesCoinMarket::collection($cms);
    file_put_contents(base_path('markets.json'), $cm->toJson());
    $bets =  Bet::all();
    $list = BetJson::collection($bets)->toJson();
    file_put_contents(base_path('bets.json'), $list);
    dd("markets => " . $cm->count(), PHP_EOL, "bets => " . $bets->count());
});

Artisan::command('teams:json', function () {
    $teams = Team::all();
    $list = $teams->map(function ($team) {
        return [
            'name' => $team->name,
            'code' => $team->code,
            'uuid' => $team->teamId,
            'country' => $team->country
        ];
    })->toJson();
    file_put_contents(base_path('teams.json'), $list);
});



Artisan::command('test:updateteamnfts', function () {
    $result = App\Support\Etherscan::updateTeamsNftMints();
    dd($result);
});

Artisan::command('test:generateGame', function () {
    $games = Game::all();
    $bookie = '0xf0C742ec7C801a6aFC99F9D41c92c06EBef2Cf91';
    $games->each(function (Game $game) use ($bookie) {
        $game->generateGameId($bookie)->save();
    });
});



Artisan::command('test:json', function () {
    $cms = Market::query()
        ->with(['bets'])
        ->get();
    $cases = $cms->map(function ($cm) {
        return [
            'test' => $cm->slug,
            'marketId' =>  $cm->marketId,
            'bets' => $cm->bets->map(function (Bet $bet) {
                return [
                    'betId' => $bet->betId,
                    'result' => $bet->test,
                    'test' => $bet->name,
                ];
            })
        ];
    });
    file_put_contents(base_path('tests.json'), json_encode($cases));
});


Artisan::command('markets:json', function () {
    $chain = Chain::with(['coinMarkets'])->where('chainId', 56)->first();
    $cm = ResourcesCoinMarket::collection($chain->coinMarkets);
    file_put_contents(base_path('markets.json'), $cm->toJson());
});


Artisan::command('community:nfts', function () {
    CommunityNftMetadata::bets();
    CommunityNftMetadata::markets();
});



Artisan::command('gm', function () {
    $game = [
        'homeTeam' => 2,
        'awayTeam' => 1,
        'startTime' => 1665500793,
        'endTime' => 1665506793,
        'players' => [0]
    ];
    $hash = '0x8e2ab1c07445273fb3c231013bbe0df24c22bc91e5ff01ae172fe46fb89b7a3e';
    $type = json_decode('{
          "components": [
            {
              "internalType": "NftId",
              "name": "homeTeam",
              "type": "uint256"
            },
            {
              "internalType": "NftId",
              "name": "awayTeam",
              "type": "uint256"
            },
            {
              "internalType": "uint256",
              "name": "startTime",
              "type": "uint256"
            },
            {
              "internalType": "uint256",
              "name": "endTime",
              "type": "uint256"
            },
            {
              "internalType": "NftId[]",
              "name": "players",
              "type": "uint256[]"
            }
          ],
          "internalType": "struct GameKey",
          "name": "gameKey",
          "type": "tuple"
        }');
    $encoded = ABI::EncodeTuple($type, array_values($game));
    $marketId = '0x' . Keccak::hash(hex2bin(substr($encoded, 2)), 256);
    dd($marketId, $hash);
});
Artisan::command('f:l', function () {
    Football::updateLeagues();
});

Artisan::command('f:t', function () {
    Football::updateTeams(leagueIds: [39, 78, 180]);
});

Artisan::command('ff', function () {
    Football::updateGames(leagueIds: [39, 78, 180]);
});

Artisan::command('slug', function () {
    $all = Game::all();
    $all->each(function (Game $game) {
        $game->slug = Str::slug($game->name . '-' . $game->startTime->format('Y-m-d-h-i'));
        $game->save();
    });
});

Artisan::command('gs', function () {
    $markets = Market::query()->pluck('id')->all();
    $games = Game::all();
    $games->each(function (Game $game) use ($markets) {
        $game->markets()->sync($markets);
    });
});

Artisan::command('test:etherscan', function () {
    Etherscan::updateSlips();
});


Artisan::command('btx', function () {
    $slipId = "73938750240448849594274404439738504652771047358227879739161556762670670394814";
    $encoded = ABI::EncodeParameters_External(
        ['uint256', 'uint256', 'bytes32', 'bytes32', 'bytes32', 'address', 'address', 'uint256'],
        [
            '10000000000000000',
            '0',
            '0x247fd94705a7726a87669109aabd5f9559eead3ec732930f4c2fd0b3d0b5e4c8',
            '0x833aea91e37d4070293778c9e8c234220b2128a9fca79730f85ff4115f20ea88',
            '0x3aac4c0ff25f18a847eebc6dac47b7e8c633f27a210353b323a34cf416b01da9',
            '0x0000000000000000000000000000000000000000',
            '0x921Cf70FA8da484E7a3EB1cB52b75A120Efa612f',
            '83441978105169301247883934277131339877'
        ]
    );
    $betId = '0x' . Keccak::hash(hex2bin(substr($encoded, 2)), 256);
    $slip_id = new BigInteger($betId, 16);
    $slip_id = $slip_id->toString();
    // reverser
    $slipIdHexa = new BigInteger($slip_id);
    $slipIdHex = "0x" . $slipIdHexa->toHex();
    dd($slip_id, $slipId, $betId, $slipIdHex);
});

Artisan::command('gu', function () {
    //$game = Game::where('slug', 'newcastle-vs-aston-villa-2024-01-30-08-15')->get();
    $game = Game::where('slug', 'newcastle-vs-aston-villa-2024-01-30-08-15')->first();
    //Football::updateLiveGame($game);
    $results = Result::query()
        ->with(['bet', 'game'])
        ->where('game_id', $game->id)->get();
    foreach ($results as $key =>  $result) {
        $result->update([
            'winner' => $result->won()
        ]);
    }
});

Artisan::command('oxyl2', function () {
    $uk = [
        ['name' => 'Premier League', 'id' => 39],
        ['name' => 'EFL Championship', 'id' => 46,],
        ['name' => 'EFL League One', 'id' => 41,],
        ['name' => 'EFL League Two', 'id' => 42,],
        ['name' => 'England Isthmian League North Division', 'id' => 52],
        ['name' => 'England Isthmian League South Central Division', 'id' => 53,],
        ['name' => 'England Professional Development League', 'id' => 703,],
        ['name' => 'England Southern League Premier Division Central', 'id' => 933],
        ['name' => 'England Southern League Premier Division South', 'id' => 56],
        ['name' => 'English FA Cup', 'id' => 45,],
        ['name' => 'English National League', 'id' => 43],
        ['name' => 'English National League North', 'id' => 50],
        ['name' => 'English National League South', 'id' => 51],
        ['name' => 'Northern Irish League Cup', 'id' => 559],
        ['name' => 'Northern Irish NIFL Premiership', 'id' => 408],
        ['name' => 'Scottish Premiership', 'id' => 179],
        ['name' => 'Scottish Championship', 'id' => 180,],
        ['name' => 'Scottish League One', 'id' => 183],
        ['name' => 'Scottish League Two', 'id' => 184],
        ['name' => 'Welsh Premier League', 'id' => 112,],
    ];
    $eu = [
        ['name' => 'UEFA Champions League', 'id' => 2,],
        ['name' => 'UEFA Europa League', 'id' => 3,],
        ['name' => 'UEFA Europa Conference League', 'id' => 848,],
        ['name' => 'Italian Serie A', 'id' => 135,],
        ['name' => 'Spanish La Liga', 'id' => 140,],
        ['name' => 'Dutch Eredivisie', 'id' => 88,],
        ['name' => 'French Ligue 1', 'id' => 61,],
        ['name' => 'German Bundesliga', 'id' => 78,],
        ['name' => 'Portuguese Primeira Liga', 'id' => 94,],
        ['name' => 'Austrian 2. Liga', 'id' => 219,],
        ['name' => 'Austrian Bundesliga', 'id' => 218,],
        ['name' => 'Belgian Cup', 'id' => 147,],
        ['name' => 'Belgian First Division A', 'id' => NULL,],
        ['name' => 'Belgian First Division B', 'id' => NULL,],
        ['name' => 'Bulgarian First Division', 'id' => 172,],
        ['name' => 'Croatia HNL', 'id' => 210,],
        ['name' => 'Croatian Cup', 'id' => 212,],
        ['name' => 'Czech Republic Premier League', 'id' => NULL,],
        ['name' => 'Danish 1.Division', 'id' => NULL,],
        ['name' => 'Danish Superliga', 'id' => NULL,],
        ['name' => 'Denmark Landspokal', 'id' => NULL,],
        ['name' => 'Dutch Eerste Divisie', 'id' => NULL,],
        ['name' => 'French Ligue 2', 'id' => NULL,],
        ['name' => 'French Ligue National', 'id' => NULL,],
        ['name' => 'Georgia Erovnuli Liga', 'id' => NULL,],
        ['name' => 'German 2.Bundesliga', 'id' => NULL,],
        ['name' => 'German 3.Liga', 'id' => NULL,],
        ['name' => 'German DFB Pokal', 'id' => NULL,],
        ['name' => 'German Regionalliga Südwest', 'id' => NULL,],
        ['name' => 'Greece Super League 2', 'id' => NULL,],
        ['name' => 'Greek Cup', 'id' => NULL,],
        ['name' => 'Greek Super League', 'id' => NULL,],
        ['name' => 'Hungarian NB I', 'id' => NULL,],
        ['name' => 'Italian Coppa Italia', 'id' => NULL,],
        ['name' => 'Italian Serie B', 'id' => NULL,],
        ['name' => 'Norwegian First Division', 'id' => NULL,],
        ['name' => 'Norwegian Premier League', 'id' => NULL,],
        ['name' => 'Poland II liga', 'id' => NULL,],
        ['name' => 'Polish Ekstraklasa', 'id' => NULL,],
        ['name' => 'Polish I liga', 'id' => NULL,],
        ['name' => 'Portugal Liga 2', 'id' => NULL,],
        ['name' => 'Premier League International Cup', 'id' => NULL,],
        ['name' => 'Romania Liga II', 'id' => NULL,],
        ['name' => 'Romanian Liga 1', 'id' => NULL,],
        ['name' => 'Slovak Super Liga', 'id' => NULL,],
        ['name' => 'Slovakia Republic Cup', 'id' => NULL,],
        ['name' => 'Slovenian Prva Liga', 'id' => NULL,],
        ['name' => 'Spanish Copa del Rey', 'id' => NULL,],
        ['name' => 'Spanish La Liga 2', 'id' => NULL,],
        ['name' => 'Swiss Challenge League', 'id' => NULL,],
        ['name' => 'Swiss Super League', 'id' => NULL,],
        ['name' => 'Turkish 1. Lig', 'id' => NULL,],
        ['name' => 'Turkish Super Lig', 'id' => NULL,],
        ['name' => 'UEFA Youth League', 'id' => NULL,],
        ['name' => 'Ukrainian Premier League', 'id' => NULL,],
    ];



    $data = json_decode(file_get_contents('leagues.json'));
    $current = collect($data->response)->filter(function ($lg) {
        $years = collect($lg->seasons)->pluck('year');
        return $years->contains(2023);
    });
    dd($current);
});

if (!function_exists('betN')) {
    function betN($position)
    {
        $principal = 250;
        $payout = 60;
        $final = $principal * pow((1 - $payout / (2 * $principal)), $position);
        return ($final / $principal) *  $payout;
    }
}


Artisan::command('betn', function () {
    $amount = betN(10);
    $amounts = [];
    $minVotes = 251;
    for ($i = 0; $i < 500; $i++) {
        $amount = betN($i);
        if ($amount < 1) {
            break;
        }
        $amounts[] = $amount;
    }
    $minVotes = (count($amounts) / 2) + 2;
    dd($amounts, array_sum($amounts), $minVotes);
});
