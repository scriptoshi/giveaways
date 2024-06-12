<?php

use App\Http\Controllers\AdsController;
use App\Http\Controllers\ConnectionsController;
use App\Http\Controllers\ContributionsController;
use App\Http\Controllers\GiveawaysController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaunchpadsController;
use App\Http\Controllers\MintsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\QuestersController;
use App\Http\Controllers\QuestsController;
use App\Http\Controllers\S3Controller;
use App\Http\Controllers\SocialsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TopupsController;
use App\Http\Controllers\Web3Controller;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::name('socials.')->controller(SocialsController::class)
    ->group(function () {
        Route::get('/go/{to}', 'go')->name('go');
    });
Route::name('socials.')->controller(SocialsController::class)
    ->group(function () {
        Route::get('/go/{to}', 'go')->name('go');
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


Route::controller(ConnectionsController::class)
    ->name('connections.')
    ->group(function () {
        Route::post('connect/{provider}', 'connect')->name('connect');
        Route::get('discord/add-bot', 'addDiscordBot')->name('discord.add.bot');
        Route::any('discord/bot-added', 'discordBotAdded')->name('discord.bot.added');
        Route::any('discord/check/invite', 'checkDiscordInviteHasBot')->name('check.invite');
        Route::any('telegram/check/bot', 'checkTelegramHasBot')->name('check.telegram');
        Route::any('callback/{provider}/{uuid?}', 'callback')->name('callback');
    });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::name('user.')
        ->controller(UsersController::class)
        ->group(function () {
            Route::post('/activate', 'activate')->name('activate');
        });
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



Route::name('projects.')->controller(ProjectsController::class)->group(function () {
    Route::get('/projects', 'index')->name('index');
    Route::get('/me', 'mine')->name('mine')->middleware(['auth:sanctum', 'verified']);
    Route::get('/@{project:slug}', 'show')->name('show');
    Route::get('/projects/create', 'create')->name('create');
    Route::post('projects/check-username', 'checkUsername')->name('check.username');
    Route::post('/projects/store', 'store')->name('store')->middleware(['auth:sanctum', 'verified']);
    Route::post('/projects/deploy-store', 'deployStore')->name('deploy.store')->middleware(['auth:sanctum', 'verified']);
    Route::post('/projects/messages/{project:uuid}/{uuid?}', 'saveMessages')->name('messages.save')->middleware(['auth:sanctum', 'verified']);;
    Route::put('/projects/{project:uuid}/update', 'update')->name('update')->middleware(['auth:sanctum', 'verified']);
    Route::get('/projects/{project:uuid}', 'edit')->name('edit')->middleware(['auth:sanctum', 'verified']);
    Route::delete('/projects/{project:uuid}/delete', 'delete')->middleware(['auth:sanctum', 'verified'])->name('delete');
});


Route::name('services.')->controller(AdsController::class)->group(function () {
    Route::get('/services', 'index')->name('index');
    Route::get('/service/{ad:slug}', 'show')->name('show');
    Route::get('/services/create', 'create')->name('create');
    Route::post('/services/store', 'store')->name('store')->middleware(['auth:sanctum', 'verified']);
    Route::post('/services/messages/{ad:uuid}/{uuid?}', 'saveMessages')->name('messages.save')->middleware(['auth:sanctum', 'verified']);;
    Route::put('/services/{ad:uuid}/update', 'update')->name('update')->middleware(['auth:sanctum', 'verified']);
    Route::get('/services/{ad:uuid}', 'edit')->name('edit')->middleware(['auth:sanctum', 'verified']);
    Route::delete('/services/{ad:uuid}/destroy', 'destroy')->middleware(['auth:sanctum', 'verified'])->name('destroy');
});

Route::name('giveaways.')->controller(GiveawaysController::class)->group(function () {
    Route::get('/crypto-give-aways', 'index')->name('index');
    Route::get('/crypto-give-away/{giveaway:slug}', 'show')->name('show');
    Route::get('/crypto-give-aways/create', 'create')->name('create');
    Route::post('/giveaways/store', 'store')->name('store')->middleware(['auth:sanctum', 'verified']);
    Route::post('/giveaways/code/{giveaway:uuid}', 'bonusCode')
        ->name('bonus.code')
        ->middleware(['auth:sanctum', 'verified']);;
    Route::post('/giveaways/messages/{giveaway:uuid}/{uuid?}', 'saveMessages')->name('messages.save')->middleware(['auth:sanctum', 'verified']);;
    Route::put('/giveaways/{giveaway:uuid}/update', 'update')->name('update')->middleware(['auth:sanctum', 'verified']);
    Route::put('/giveaways/{giveaway:uuid}/stop', 'stop')->name('stop')->middleware(['auth:sanctum', 'verified']);
    Route::get('/crypto-give-aways/{giveaway:uuid}', 'edit')->name('edit')->middleware(['auth:sanctum', 'verified']);
    Route::get('/give-away-tasks/{giveaway:uuid}', 'tasks')->name('tasks')->middleware(['auth:sanctum', 'verified']);
    Route::delete('/giveaways/{giveaway:uuid}/delete', 'delete')->middleware(['auth:sanctum', 'verified'])->name('delete');
});


Route::name('quests.')->controller(QuestsController::class)->group(function () {
    Route::get('/check-task/registered', 'checkTask')->name('check.task');
    Route::post('/quests/store/{giveaway:uuid}', 'store')->name('store')->middleware(['auth:sanctum', 'verified']);
    Route::post('/quests/update/{quest}', 'update')->name('update')->middleware(['auth:sanctum', 'verified']);
});

Route::name('questers.')
    ->middleware(['auth:sanctum', 'verified'])
    ->controller(QuestersController::class)
    ->group(function () {
        Route::get('/sleep', 'sleep')->name('sleep');
        Route::post('/questers/pump/{quester}', 'pump')->name('pump');
        Route::post('/questers/boost/{quester}', 'boost')->name('boost');
        Route::post('/questers/claim-sleep', 'claimSleep')->name('claim.sleep');
        Route::post('/questers/claimed-sleep/{quester}', 'claimedSleep')->name('claimed.sleep');
        Route::post('/questers/claim/{quester}', 'claim')->name('claim');
        Route::post('/questers/claimed/{quester}', 'claimed')->name('claimed');
    });


Route::name('tasks.')->controller(TasksController::class)->group(function () {
    Route::post('/check/{quest}', 'check')
        ->name('check')
        ->middleware(['auth:sanctum', 'verified']);
    Route::post('/check-all/{giveaway:uuid}', 'checkAll')
        ->name('check.all')
        ->middleware(['auth:sanctum', 'verified']);
});



Route::name('launchpads.')->controller(LaunchpadsController::class)->group(function () {
    Route::post('/launchpads/store/{project:uuid}', 'store')->name('store')->middleware(['auth:sanctum', 'verified']);
});


Route::name('contributions.')->controller(ContributionsController::class)->group(function () {
    Route::post('/contributions/store/{launchpad}', 'store')->name('store');
});


#topups
Route::name('topups.')->controller(TopupsController::class)->group(function () {
    Route::post('/topups/store/{giveaway}', 'store')->name('store');
});
#topups

#NFT

Route::name('mints.')
    ->middleware(['auth:sanctum', 'verified'])
    ->controller(MintsController::class)
    ->group(function () {
        Route::get('/access', 'access')->name('access');
        Route::post('/mints/store', 'store')->name('store');
    });
