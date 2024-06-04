<?php

namespace App\Actions\Socialite;

use App\Enums\ConnectionProvider;
use App\Models\User;
use Laravel\Socialite\Two\User as Social;

class CreateConnection
{


    /**
     * Create a connection  the user.
     */
    public function createConnection(User $user, Social $social, ConnectionProvider $provider): \App\Models\Connection
    {
        $connection = $user->connections()->updateOrCreate([
            'userId' =>  $social->getId(),
            'provider' =>  $provider,
        ], [
            'username' =>  $social->getNickname(),
            'name' =>  $social->getName(),
            'email' => $social->getEmail() ?? $social->getRaw()['email'] ?? null,
            'token' => $social->token,
            'refreshToken' => $social->refreshToken,
            'expiresIn' => $social->expiresIn,
            'expires_at' => now()->addSeconds((int)$social->expiresIn)->subMinutes(5),
        ]);
        return $connection;
    }
}
