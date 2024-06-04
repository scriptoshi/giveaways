<?php

namespace App\Enums;

enum ConnectionProvider: string
{
    case GOOGLE = 'google';
    case TWITTER = 'twitter';
    case TELEGRAM = 'telegram';
    case DISCORD = 'discord';

    public function connector(): string
    {
        return match ($this) {
            ConnectionProvider::TWITTER => 'twitter-oauth-2',
            ConnectionProvider::TELEGRAM => 'telegram',
            ConnectionProvider::DISCORD => 'discord',
        };
    }
    public function scopes(): array
    {
        return match ($this) {
            ConnectionProvider::TWITTER => ['offline.access', 'tweet.read', 'users.read'],
            ConnectionProvider::TELEGRAM => [],
            ConnectionProvider::DISCORD => ["email", "guilds", "guilds.join", "guilds.members.read", "identify"],
        };
    }
}
