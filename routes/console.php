<?php

use App\Actions\SelectWinners;
use App\Enums\QuesterStatus;
use App\Models\Giveaway;
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

/**
 * select winners
 */
Artisan::command('select:winners', function () {
    $giveways = Giveaway::query()
        ->where('ends_at', '<', now())
        ->whereNull('winner_selected_at')
        ->get();
    $giveways->each(function (Giveaway $giveaway) {
        app(SelectWinners::class)->selectWinnerFor($giveaway);
    });
});



Artisan::command('update:galxe', function () {
    $questers = Quester::query()
        ->where('status', QuesterStatus::COMPLETED)
        ->with(['giveaway', 'user.account'])
        ->get();
    $questers->each(function (Quester $quester) {
        Galxe::giveaway($quester);
    });
});

Artisan::command('access:json', function () {
    $data = [
        'name' => 'Gas Finance Access Badge',
        'description' => ' The giveawaysfinance access badge grants you limited access to claim sleep tokens earned via the quest rewards system from the sleep rewards contract',
        'external_url' => 'https://giveaways.finance/access',
        'image' => 'https://nft.giveaways.finance/sleep-finance-access.png',
        'attributes' => [
            ["trait_type" => "Access", "value" => 'Quest Claims'],
            ["trait_type" => "Level", "value" => 1, 'display_type' => 'number'],
            ["trait_type" => "Reward", "value" => 'GAS Tokens'],
            ["trait_type" => "Validity", "value" => 'One Year'],
        ]
    ];
    file_put_contents('sleep-finance-access.json', json_encode($data));
});
