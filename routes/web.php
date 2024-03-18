<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaunchpadsController;
use App\Http\Controllers\S3Controller;
use App\Http\Controllers\TokensController;
use App\Http\Controllers\Web3Controller;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::controller(HomeController::class)
    ->group(function () {
        Route::get('/', 'home')->name('home');
        Route::get('/privacy', 'privacy')->name('privacy');
        Route::get('/terms', 'terms')->name('terms');
        Route::post('/lang', 'lang')->name('lang');
        Route::get('/gas/{chainId}', 'gas')->name('gas');
        Route::get('/trending', 'trending')->name('home.trending');
        Route::get('/explore/{item?}', 'home')->name('explore');
    });

Route::controller(S3Controller::class)
    ->group(function () {
        Route::post('sign/{disk?}/{folder?}', 'sign')->name('s3.sign');
    });

Route::controller(Web3Controller::class)
    ->group(function () {
        Route::post('status', 'status')->name('status');
        Route::post('nonce', 'nonce')->name('nonce');
        Route::get('accounts', 'accounts')->middleware(['auth:sanctum', 'verified'])->name('accounts.index');
        Route::post('account/add', 'addAccount')->middleware(['auth:sanctum', 'verified'])->name('account.add');
        Route::delete('account/delete/{id}', 'deleteAccount')->middleware(['auth:sanctum', 'verified'])->name('accounts.delete');
    });

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::name('user.')
        ->controller(UsersController::class)
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('user/requests', 'manage_requests')->name('manage_requests');
            Route::get('user/post_requests', 'post_request')->name('post_requests');
            Route::get('user/referrals', 'referrals')->name('referrals');
            Route::get('user/referrals/activities', 'activities')->name('referrals.activities');
            Route::get('user/settings', 'settings')->name('settings');
            Route::post('user/chain/set', 'setChain')->name('chain.set');
            Route::post('user/accounts/multiple', 'multiple')->name('accounts.multiple');
            Route::delete('user/accounts/{account_id}', 'deleteAccount')->name('accounts.delete');
        });
});


Route::name('tokens.')->controller(TokensController::class)->group(function () {
    Route::get('/tokens', 'index')->middleware(['auth:sanctum', 'verified'])->name('index');
    Route::get('/crosschain/{chainId}/{token}', 'crosschain')->name('crosschain');
    Route::get('/tokens/create/{chainId}/{type}', 'create')->name('create');
    Route::post('/tokens/store', 'store')->middleware(['auth:sanctum', 'verified'])->name('store');
    Route::post('/tokens/crosschain/check', 'checkCrosschain')->middleware(['auth:sanctum', 'verified'])->name('crosschain.check');
    Route::put('/tokens/tokens/{token}', 'toggle')->middleware(['auth:sanctum', 'verified'])->name('toggle');
    Route::put('/tokens/logo/{token}', 'updateLogo')->middleware(['auth:sanctum', 'verified'])->name('update.logo');
    Route::put('/tokens/whitelist/add/{token}', 'addWhiteList')->name('whitelist.add');
    Route::put('/tokens/whitelist/remove/{token}', 'removeWhiteList')->name('whitelist.remove');

    Route::get('/token/{chainId}/{token}', 'show')->name('show');
    Route::put('/tokens/{token}', 'update')->middleware(['auth:sanctum', 'verified'])->name('update');
    Route::delete('/tokens/{token}', 'destroy')->middleware(['auth:sanctum', 'verified'])->name('destroy');
});



#launchpads
Route::name('launchpads.')->controller(LaunchpadsController::class)->group(function () {

    Route::post('/launchpads/list', 'list')->name('list');
    Route::post('/launchpads/extras', 'extras')->name('extras');
    Route::get('/launchpads/edit/{chainId}/{launchpad}', 'edit')->name('edit');
    Route::get('/{launchpad}/create/{chainId}', 'create')
        ->name('create')
        ->whereIn('launchpad', ['fairlaunch', 'fairliquidity', 'launchpad', 'privatesale']);

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/launchpads/statuses', 'statuses')->name('statuses');
        //step3
        Route::get('/{launchpads}/project/{chainId}/{address}', 'project')
            ->name('project')
            ->whereIn('launchpads', ['fairlaunch', 'fairliquidity', 'launchpad', 'privatesale']);
        Route::get('/{launchpads}/founders/{chainId}/{address}', 'founders')
            ->name('founders')
            ->whereIn('launchpads', ['fairlaunch', 'fairliquidity', 'launchpad', 'privatesale']);
        //step 1/2
        Route::get('/{launchpads}/deploy/{chainId}/{type}/{coin}/{token}', 'deploy')
            ->name('deploy')
            ->whereIn('launchpads', ['fairlaunch', 'fairliquidity', 'launchpad', 'privatesale']);;
        Route::post('/launchpads/hate/{launchpad}', 'hate')->name('hate');
        Route::post('/launchpads/love/{launchpad}', 'love')->name('love');
        Route::post('/launchpads/unlike/{launchpad}', 'unlike')->name('unlike');
        Route::post('/launchpads/like/{launchpad}', 'like')->name('like');
        Route::post('/launchpads/finalize/{launchpad}', 'finalize')->name('finalize');
        Route::post('/launchpads/unsubscribe/{launchpad}', 'unsubscribe')->name('unsubscribe');
        Route::post('/launchpads/subscribe/{launchpad}', 'subscribe')->name('subscribe');
        Route::post('/launchpads/status/{launchpad}', 'status')->name('status');
        Route::post('/launchpads/store', 'store')->name('store');
        Route::post('/launchpads/sync', 'sync')->name('sync');
    });
    Route::get('/launchpads/{chain?}', 'index')->name('index');
    Route::get('/launchpads/{launchpad}/json', 'view')->name('view');
    Route::get('/launchpads/{chainId}/{launchpad}', 'show')->name('show');
});
