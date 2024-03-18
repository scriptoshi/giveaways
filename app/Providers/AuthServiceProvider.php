<?php

namespace App\Providers;

use App\Models\Nft;
use App\Models\Offer;
use App\Models\Team;
use App\Policies\NftPolicy;
use App\Policies\OfferPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {


        //
    }
}
