<?php

namespace App\Http\Controllers;

use App\Support\Discord;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\UrlGenerator;

class DiscordBotCallback  extends Controller
{
    function __invoke(Request $request, UrlGenerator $urlGenerator, Client $client)
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
                    'code'          => $request->get('code'),
                    // this endpoint is never hit, it just needs to be here for OAuth compatibility
                    'redirect_uri' => $urlGenerator->to(Discord::callbackUrl() . '/bot-added'),
                ],
            ]);
        } catch (Exception $e) {
            return $botAddedHandler->errored($e);
        }
        $json = \GuzzleHttp\Utils::jsonDecode($response->getBody()->getContents());
        return $botAddedHandler->botAdded(
            $json->access_token,
            $json->expires_in,
            $json->refresh_token,
            $json->guild
        );
    }
}
