<?php


use App\Models\Connection;
use App\Support\LangCleanup;
use App\Support\Telegram;
use App\Support\Twitter;
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


Artisan::command('e:v', function () {
    $response = Curl::to('http://127.0.0.1/check-task/registered')
        ->returnResponseObject()
        ->asJson()
        ->withData([
            'addresses' => 'rtrr'
        ])->get();
    dd($response);
});
Artisan::command('d:v', function () {
    dd(Telegram::checkBotAccess('sleepfinance'));
});
