<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ChainsController;
use App\Http\Controllers\Admin\CoinsController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AmmsController;
use App\Http\Controllers\Admin\BadgesController;
use App\Http\Controllers\Admin\BoostsController;
use App\Http\Controllers\Admin\FactoriesController;
use App\Http\Controllers\Admin\LaunchpadsController;
use App\Http\Controllers\Admin\ProjectsController;
use App\Http\Controllers\Admin\ServicesController as AdminServicesController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use Illuminate\Support\Facades\Route;


Route::controller(SettingController::class)
    ->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::get('/settings', 'settings')->name('settings');
        Route::post('/settings', 'update')->name('settings.update');
        Route::put('/chains/toggle/{chainId}', 'toggleChain')->name('toggle');
    });
Route::controller(AdminController::class)
    ->group(function () {
        Route::get('/tokens', 'tokens')->name('tokens');
        Route::get('/staking', 'staking')->name('staking');
        Route::get('/launchpads', 'launchpads')->name('launchpads');
        Route::get('/fairlaunch', 'fairlaunch')->name('fairlaunch');
        Route::get('/fairlaunch', 'fairlaunch')->name('fairlaunch');
        Route::get('/airdrops', 'airdrops')->name('airdrops');
        Route::get('/token-lock', 'tokenlock')->name('tokenlock');
        Route::get('/liquidity-lock', 'liquiditylock')->name('liquiditylock');
        Route::get('/launchpad-factory/{chainId}', 'launchpadfactory')->name('launchpadfactory');
        Route::get('/token-factory/{chainId}', 'tokenfactory')->name('tokenfactory');
        Route::get('/staking-factory/{chainId}', 'stakingfactory')->name('stakingfactory');
        Route::get('/fairlaunch-factory/{chainId}', 'fairfactory')->name('fairfactory');
        Route::get('/privatesale-factory/{chainId}', 'privatesalefactory')->name('privatesalefactory');
        Route::get('/tokentools/{chainId}', 'tokentools')->name('tokentools');
        Route::get('/defi-factory/{chainId}', 'defifactory')->name('defifactory');
    });

Route::name('amms.')->controller(AmmsController::class)->group(function () {
    Route::get('/amms', 'index')->name('index');
    Route::get('/amms/create', 'create')->name('create');
    Route::post('/amms/store', 'store')->name('store');
    Route::get('/amms/{amm}/show', 'show')->name('show');
    Route::get('/amms/{amm}/edit', 'edit')->name('edit');
    Route::put('/amms/{amm}', 'update')->name('update');
    Route::put('/amms/toggle/{amm}', 'toggle')->name('toggle');
    Route::delete('/amms/{amm}', 'destroy')->name('destroy');
});

Route::name('chains.')->controller(ChainsController::class)->group(function () {
    Route::get('/chains', 'index')->name('index');
    Route::put('/chains-many', 'updateMany')->name('update.many');
    Route::get('/chains/create', 'create')->name('create');
    Route::post('/chains/store', 'store')->name('store');
    Route::get('/chains/{chain}/show', 'show')->name('show');
    Route::get('/chains/{chain}/edit', 'edit')->name('edit');
    Route::put('/chains/{chain}', 'update')->name('update');
    Route::put('/chains/toggle/{chain}', 'toggle')->name('toggle');
    Route::delete('/chains/{chain}', 'destroy')->name('destroy');
});

Route::name('coins.')->controller(CoinsController::class)->group(function () {
    Route::get('/coins', 'index')->name('index');
    Route::put('/coins-many', 'updateMany')->name('update.many');
    Route::get('/coins/create', 'create')->name('create');
    Route::post('/coins/store', 'store')->name('store');
    Route::get('/coins/{coin}/show', 'show')->name('show');
    Route::get('/coins/{coin}/edit', 'edit')->name('edit');
    Route::put('/coins/{coin}', 'update')->name('update');
    Route::put('/coins/toggle/{amm}', 'toggle')->name('toggle');
    Route::delete('/coins/{coin}', 'destroy')->name('destroy');
});

Route::name('badges.')->controller(BadgesController::class)->group(function () {
    Route::get('/badges', 'index')->name('index');
    Route::get('/badges/create', 'create')->name('create');
    Route::post('/badges/store', 'store')->name('store');
    Route::get('/badges/{badge}/edit', 'edit')->name('edit');
    Route::get('/badges/{projectId}/{badgeId}', 'show')->name('show');
    Route::put('/badges/{badge}', 'update')->name('update');
    Route::put('/badges/toggle/{badge}', 'toggle')->name('toggle');
    Route::delete('/badges/{badge}', 'destroy')->name('destroy');
});

Route::name('users.')->controller(AdminUsersController::class)->group(function () {
    Route::get('/users', 'index')->name('index');
    //Route::get('/users/{user}/edit', 'edit')->name('edit');
    //Route::put('/users/{user}', 'update')->name('update');
    //Route::delete('/users/{user}', 'destroy')->name('destroy');
});


#factories
Route::name('factories.')->controller(FactoriesController::class)->group(function () {
    Route::get('/factories', 'index')->name('index');
    Route::get('/factories/create/{factory}', 'create')->name('create');
    Route::post('/factories/store', 'store')->name('store');
    Route::get('/factories/{factory}/show', 'show')->name('show');
    Route::get('/factories/{factory}/edit', 'edit')->name('edit');
    Route::post('/factories/version/{factory}', 'version')->name('version');
    Route::put('/factories/{factory}', 'update')->name('update');
    Route::delete('/factories/{factory}', 'destroy')->name('destroy');
});
#factories


Route::name('launchpads.')->controller(LaunchpadsController::class)->group(function () {
    Route::get('/launchpads', 'index')->name('index');
    Route::get('/launchpads/{launchpad}/edit', 'edit')->name('edit');
    Route::put('/launchpads/{launchpad}', 'update')->name('update');
    Route::put('/launchpads/toggle/{launchpad}', 'toggle')->name('toggle');
    Route::put('/launchpads/badges/{launchpad}', 'badges')->name('badges');
    Route::delete('/launchpads/{launchpad}', 'destroy')->name('destroy');
});

Route::name('projects.')->controller(ProjectsController::class)
    ->group(function () {
        Route::get('/projects', 'index')->name('index');
        Route::get('/projects/{project:uuid}/edit', 'edit')->name('edit');
        Route::put('/projects/{project:uuid}', 'update')->name('update');
        Route::put('/projects/toggle/{project:uuid}', 'toggle')->name('toggle');
        Route::put('/projects/badges/{project:uuid}', 'badges')->name('badges');
        Route::delete('/projects/{project:uuid}', 'destroy')->name('destroy');
    });


#factories
Route::name('boosts.')->controller(BoostsController::class)->group(function () {
    Route::get('/boosts', 'index')->name('index');
    Route::get('/boosts/create', 'create')->name('create');
    Route::post('/boosts/store', 'store')->name('store');
    Route::delete('/boosts/{boost}', 'destroy')->name('destroy');
});
#factories
