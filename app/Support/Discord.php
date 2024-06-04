<?php

namespace App\Support;

use Ixudra\Curl\Facades\Curl;
use App\Models\Connection;

class Discord
{
    public const base = 'https://discord.com/api/';



    //'Authorization:
    public function __construct(public Connection $connection)
    {
    }


    public static function key()
    {
        return  config('services.discord.client_id');
    }

    public static function secret()
    {
        return  config('services.discord.client_secret');
    }


    public static function callbackUrl(): string
    {
        return   route('connections.discord.bot.added');
    }


    public static function addBotUrl(int $permissions, ?int $guildId = null, array $scopes = [])
    {
        $scopes = collect([...$scopes, 'bot'])->implode(' ');
        $urlScopes = urlencode($scopes);
        $url = static::base . '/oauth2/authorize?client_id=' . self::key() . '&scope=' . $urlScopes . '&permissions=' . $permissions . '&redirect_uri=' . urlencode(self::callbackUrl()) . '&response_type=code';
        if ($guildId != null) {
            $url .= '&guild_id=' . $guildId;
        }
        return $url;
    }

    /**
     * @param $discordUserId $connection->userId
     */
    public static function joined($guildId, $discordUserId)
    {
        $bot = config('services.discord.bot_token');
        $api = static::base;
        $uri = "{$api}/guilds/{$guildId}/members/{$discordUserId}";
        $user = Curl::to($uri)
            ->withAuthorization("Bot {$bot}")
            ->asJsonResponse()
            ->get();
        if (isset($user?->user)) return true;
        return false;
    }


    public static function botWasAddedToInviteGuild($inviteId)
    {
        $bot = config('services.discord.bot_token');
        $api = static::base;
        $uri = "{$api}/invites/{$inviteId}";
        $invite = Curl::to($uri)
            ->withAuthorization("Bot {$bot}")
            ->asJsonResponse()
            ->get();
        return static::joined($invite->guild_id, static::key());
    }

    public static function userJoinedByInvite($inviteId, $userId)
    {
        $bot = config('services.discord.bot_token');
        $api = static::base;
        $uri = "{$api}/invites/{$inviteId}";
        $invite = Curl::to($uri)
            ->withAuthorization("Bot {$bot}")
            ->asJsonResponse()
            ->get();
        return static::joined($invite->guild_id, $userId);
    }




    protected static function discordToken(Connection $con)
    {
        if (now()->lt($con->expiresIn)) return $con->token;
        $url = 'https://discord.com/api/v10/oauth2/token';
        $response = Curl::to($url)->withData([
            'client_id' => config('services.discord.client_id'),
            'client_secret' => config('services.discord.client_secret'),
            'refresh_token' => $con->refresh_token,
            'grant_type' => 'refresh_token',
        ])->withContentType('application/x-www-form-urlencoded')
            ->post();
        $con->token = $response->access_token;
        $con->expiresIn = now()->addSeconds($response->expires_in);
        $con->refresh_token = $response->refresh_token;
        $con->save();
        return $con->token;
    }
}
