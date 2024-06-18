<?php

namespace App\Http\Middleware;

use App;
use App\Http\Resources\Account;
use App\Http\Resources\Chain as ResourcesChain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use App\Http\Resources\User as UserResource;
use App\Models\Account as ModelsAccount;
use App\Models\Chain;
use App\Models\Coin;
use Illuminate\Support\Collection;
use Storage;
use Str;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = Auth::user();
        $user?->load([
            'accounts',
        ]);
        //->loadCount(['unreadNotifications'])
        // $notifs = Auth::user()?->notifications;

        return array_merge(parent::share($request), [
            /**
             */
            'formId' => function () use ($request) {
                if ($request->session()->has('formId'))
                    return $request->session()->get('formId');
                $formId = Str::random(16);
                $request->session()->put('formId', $formId);
                return $formId;
            },
            'AuthCheck' => auth()->check(),
            'verified' => $user ? $user->hasVerifiedEmail() : null,
            'chains' => fn () => ResourcesChain::collection(Chain::with('coins')->where('active', 1)->get()),
            'login' => $request->session()->get('login', false),
            'code' => function () use ($request, $user) {
                if (!auth()->check()) return null;
                if ($request->hasCookie('referral')) return $request->cookie('referral');
                if (!$user?->referral) return null;
                $account = ModelsAccount::where('address', $user?->referral)->first();
                if (!$account) return null;
                return $account->code;
            },
            'user' => $user ? new UserResource($user) : null,
            'locale' => App::getLocale(),
            'gasChainId' => config('app.gasChainId'),
            'chainId' => $request->session()->get('chainId', 56),
            'coinId' => $request->session()->get('coinId', null),
            'coin' => fn () => Coin::find($request->session()->get('coinId', 2)),
            'chain' => fn () => Chain::find($request->session()->get('chainId', 56)),
            'admin' => str(config('app.admin'))->explode(',')->all(),
            'isAdmin' => $user ? $user->isAdmin() : false,
            'deployer' => config('app.deployer'),
            'date' => date('F-Y'),
            'status' => $request->session()->get('status'),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'config' => [
                'eip712' => config('app.eip712'),
                'appName' => config('app.name'),
                'appLogo' => Storage::disk('public')->url(config('app.logo')),
                'appDescription' => config('app.description'),
                'appUrl' => config('app.url'),
                'appLink' => config('app.link'),
                'facebookUrl' => config('app.facebookUrl'),
                'discordUrl' => config('app.discordUrl'),
                'twitterUrl' => config('app.twitterUrl'),
                'telegramUrl' => config('app.telegramUrl'),
                'telegramBot' => config('app.telegramBot'),
                'mediumUrl' => config('app.mediumUrl'),
                'uploadsDisk' => config('app.uploads_disk'),
                'nftsDisk' => config('app.nfts_disk'),
                'nftBaseURI' => config('app.nfts_disk') === 'public'
                    ? Storage::disk('public')->url('uploads')
                    : config('app.nftBaseURI'),
                's3' => config('app.uploads_disk') !== 'public',
                'ns3' => config('app.nfts_disk') !== 'public',
            ],
            'walletConnectProjectId' => config('app.walletConnectProjectId'),
            'beamsInstanceId' => config('app.beams_instance_id'),
            'csrf_token' => csrf_token()
        ]);
    }
}
