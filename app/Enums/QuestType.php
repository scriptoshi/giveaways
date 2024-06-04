<?php

namespace App\Enums;

use App\Actions\Task\Contribute as TaskContribute;
use App\Actions\Task\Verifier as TaskVerifier;
use App\Actions\Task\Twitter as TaskTwitter;
use App\Actions\Task\Telegram as TaskTelegram;
use App\Actions\Task\Discord as TaskDiscord;
use App\Actions\Task\Api as TaskApi;
use App\Actions\Task\Token as TaskToken;
use App\Actions\Task\Nft as TaskNft;


enum QuestType: string
{
    case TWITTER = 'twitter';
    case TOKEN = 'token';
    case TELEGRAM = 'telegram';
    case DISCORD = 'discord';
    case TWEET = 'tweet';
    case MINT = 'mint';
    case NFT = 'nft';
    case SWAP = 'swap';
    case CONTRIBUTE = 'contribute';
    case API = 'api';

    function verifier(): TaskVerifier
    {
        return match ($this) {
            static::CONTRIBUTE => app(TaskContribute::class),
            static::TWITTER => app(TaskTwitter::class),
            static::TWEET => app(TaskTwitter::class),
            static::TELEGRAM => app(TaskTelegram::class),
            static::DISCORD => app(TaskDiscord::class),
            static::API => app(TaskApi::class),
            static::TOKEN => app(TaskToken::class),
            static::NFT => app(TaskNft::class),
            default => app(TaskVerifier::class)
        };
    }
}
