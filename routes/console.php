<?php

use App\Enums\QuesterStatus;
use App\Models\Quester;
use App\Support\Galxe;
use App\Support\LangCleanup;
use App\Support\Site;
use Illuminate\Support\Facades\Artisan;


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


Artisan::command('site:map', function () {
    Site::map();
});

Artisan::command('update:galxe', function () {
    $questers = Quester::query()
        ->where('status', QuesterStatus::COMPLETED)
        ->with(['giveaway', 'user.account'])
        ->get();
    foreach ($questers as $quester) {
        $res = Galxe::giveaway($quester);
        dump($res);
    }
});

Artisan::command('access:json', function () {
    $data = [
        'name' => 'Sleep Finance Access Badge',
        'description' => ' The sleepfinance access badge grants you limited access to claim sleep tokens earned via the quest rewards system from the sleep rewards contract',
        'external_url' => 'https://sleepfinance.io/access',
        'image' => 'https://nft.sleep.finance/sleep-finance-access.png',
        'attributes' => [
            ["trait_type" => "Access", "value" => 'Quest Claims'],
            ["trait_type" => "Level", "value" => 1, 'display_type' => 'number'],
            ["trait_type" => "Reward", "value" => 'SLEEP Tokens'],
            ["trait_type" => "Validity", "value" => 'One Year'],
        ]
    ];
    file_put_contents('sleep-finance-access.json', json_encode($data));
});
