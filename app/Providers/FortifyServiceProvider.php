<?php

namespace App\Providers;

//use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Web3\CreateNewUser;
use App\Actions\Web3\Signature;
use App\Models\Account;
use App\Models\User;
//overide login request
use App\Http\Requests\LoginRequest;
//use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
//use Laravel\Fortify\Http\Responses\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class FortifyServiceProvider extends ServiceProvider
{

    /**
     * Register any application bindings.
     *
     * @return void
     */
    public $bindings = [
        FortifyLoginRequest::class => LoginRequest::class,
        RegisterResponseContract::class =>  RegisterResponse::class
    ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Fortify::authenticateUsing(function (Request $request) {
            $account = Account::with('user')->where('address', $request->address)->first();
            $user = $account->user ?? null;
            if ($user && (new Signature)->verify($user->nonce, $request->signature, $account->address)) {
                return $user;
            }
        });

        Fortify::confirmPasswordsUsing(function (User $user, ?string $password = null) {
            $addresses =  $user->accounts()->pluck('address')->all();
            return (new Signature)->verify($user->nonce, $password, $addresses);
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $address = (string) $request->address;
            return Limit::perMinute(5)->by($address . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
