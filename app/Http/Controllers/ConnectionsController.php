<?php

namespace App\Http\Controllers;

use App\Actions\Socialite\CreateConnection;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Enums\ConnectionProvider;
use App\Models\Giveaway;
use App\Support\Discord;
use App\Support\Telegram;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Contracts\Routing\UrlGenerator;
use Str;

class ConnectionsController extends Controller
{


    /**
     * connect to a network
     * @return \Illuminate\View\View
     */
    public function connect(Request $request,  ConnectionProvider $provider)
    {
        $request->session()->put('back', $request->input('back') ?? back()->getTargetUrl());
        $url = Socialite::driver($provider->connector())
            ->scopes($provider->scopes())
            ->redirect()
            ->getTargetUrl();
        return Inertia::location($url);
    }

    /**
     * connect to a network
     * @return \Illuminate\View\View
     */
    public function callback(Request $request, ConnectionProvider $provider, $uuid = null)
    {
        $socialUser = Socialite::driver($provider->connector())->user();
        app(CreateConnection::class)->createConnection($request->user(), $socialUser, $provider);
        return redirect()
            ->to($uuid ? value(function () use ($request, $uuid) {
                $giveaway = Giveaway::where('uuid', $uuid)->first();
                if (!$giveaway) return  $request->session()->pull('back');
                return route('giveaways.show', ['giveaway' => $giveaway->slug]);
            }) : $request->session()->pull('back'))
            ->with('success', 'Account connected successfully');
    }


    /**
     * Handles connection after adding discord bot
     */
    function discordBotAdded(Request $request,  Client $client)
    {
        $botAddedHandler = app()->make(\App\Actions\Discord\BotAddedToDiscordGuild::class);
        // can happen if the user decides not to add our bot
        if ($request->has('error')) {
            return $botAddedHandler->botNotAdded($request->get('error'));
        }
        try {
            $response = $client->post('https://discord.com/api/oauth2/token', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => Discord::key(),
                    'client_secret' => Discord::secret(),
                    'code' => $request->get('code'),
                    // this endpoint is never hit, it just needs to be here for OAuth compatibility
                    'redirect_uri' =>  Discord::callbackUrl()
                ],
            ]);
        } catch (Exception $e) {
            return $botAddedHandler->botNotAdded($e->getMessage());
        }
        $json = \GuzzleHttp\Utils::jsonDecode($response->getBody()->getContents());
        return $botAddedHandler->botAdded(
            $json->access_token,
            $json->expires_in,
            $json->refresh_token,
            $json->guild
        );
    }

    public function addDiscordBot(Request $request)
    {
        session()->put('redirect', back()->getTargetUrl());
        $permissions = 67633153;
        $scopes = ConnectionProvider::DISCORD->scopes();
        // use inertia to effect redirect;
        $botUrl = Discord::addBotUrl($permissions, null, $scopes);
        return Inertia::location($botUrl);
    }


    public function checkDiscordInviteHasBot(Request $request)
    {
        $inviteId =  str(str($request->invite)->explode('/')->last())->explode('?')->first();
        $added = Discord::botWasAddedToInviteGuild($inviteId);
        return [
            'verified' => $added,
            'error' => $added ?   null : __('Bot not addedd to server')
        ];
    }

    public function checkTelegramHasBot(Request $request)
    {
        $groupName =  str(str($request->telegram)->explode('/')->last())->explode('?')->first();
        $added = Telegram::checkBotAccess($groupName);
        return [
            'verified' => $added,
            'error' => $added ?   null : __('Bot not added to group')
        ];
    }
}
