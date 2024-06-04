<?php

namespace App\Actions\Discord;

use App\Enums\ConnectionProvider;
use App\Models\Connection;
use Illuminate\Http\RedirectResponse;
use Ixudra\Curl\Facades\Curl;

class BotAddedToDiscordGuild
{

    public function botAdded($access_token, $expires_in, $refresh_token, object $guild): RedirectResponse
    {
        $user = Curl::to('https://discord.com/api/users/@me')
            ->withBearer($access_token)
            ->asJsonResponse()
            ->get();
        Connection::updateOrCreate([
            'userId' => $user->id,
            'user_id' => request()->user()->id,
            'provider' => ConnectionProvider::DISCORD,
        ], [
            'name' => $user->username,
            'username' => $user->username,
            'email' => $user->email,
            'token' => $access_token ?? null,
            'refreshToken' => $refresh_token,
            'expiresIn' => $expires_in ? now()->addSecond($expires_in) : null,
            'expires_at' => now()->addSeconds((int) $expires_in)->subMinutes(5),
        ]);
        $redirect = session()->pull('redirect');
        return redirect($redirect)->with('success', __('Bot added to discord server successfully'));;
    }

    public function botNotAdded(string $error): RedirectResponse
    {
        $redirect = session()->pull('redirect');
        return redirect($redirect)
            ->with('error', __('Adding bot to server failed :' . $error));
    }
}
