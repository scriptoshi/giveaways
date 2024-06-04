<?php

namespace App\Enums;

enum TaskType: string
{
    case TWITTER = 'twitter';
    case TOKEN = 'token';
    case TELEGRAM = 'telegram';
    case DISCORD = 'discord';
    case TWEET = 'tweet';
    case MINT = 'mint';
    case SWAP = 'swap';
    case CONTRIBUTE = 'contribute';
    case API = 'api';
}
