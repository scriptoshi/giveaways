<?php

namespace App\Support;

use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;
use Storage;

class Telegram
{

    public static function uri()
    {
        $token  = config('services.telegram.bot_token');
        return "https://api.telegram.org/bot$token";
    }

    public static function botUserId()
    {
        $token  = config('services.telegram.bot_token');
        return str($token)->explode(':')->first();
    }

    public static function validMembership(): array
    {
        return ['creator', 'administrator', 'member'];
    }



    public static function me()
    {
        return Curl::to(self::uri() . '/getMe')
            ->asJson()
            ->post();
    }


    public static function getChatMember($chatId, $userId)
    {
        return Curl::to(self::uri() . '/getChatMember')
            ->withData([
                'chat_id' => $chatId,
                'user_id' => $userId,
            ])
            ->asJson()
            ->post();
    }

    public static function getChat($username)
    {
        $name = str($username)->startsWith('@') ? $username : "@{$username}";
        return Curl::to(self::uri() . '/getChat')
            ->withData([
                'chat_id' => $name
            ])
            ->asJson()
            ->post();
    }

    public static function getChatMemberCount($username)
    {
        $name = str($username)->startsWith('@') ? $username : "@{$username}";
        return Curl::to(self::uri() . '/getChatMemberCount')
            ->withData([
                'chat_id' => $name
            ])
            ->asJson()
            ->post();
    }

    public static function checkBotAccess($username)
    {
        $groupName = str($username)->startsWith('@') ? $username : "@{$username}";
        $chat = static::getChat($groupName);
        if (isset($chat->result)) {
            $botUserId = static::botUserId(); //
            $user = static::getChatMember($chat->result->id, $botUserId);
            if (in_array($user->result->status, static::validMembership())) return true;
            return false;
        }
        return false;
    }

    public static function checkUserJoined($group, $userId): bool
    {
        $groupName = str($group)->startsWith('@') ? $group : "@{$group}";
        $chat = static::getChat($groupName);
        if (isset($chat->result)) {
            $user = static::getChatMember($chat->result->id, $userId);
            if (in_array($user->result->status, static::validMembership())) return true;
            return false;
        }
        return false;
    }




    public static function getWebhookInfo()
    {
        return Curl::to(self::uri() . '/getWebhookInfo')->post();
    }
}
