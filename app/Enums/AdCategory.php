<?php

namespace App\Enums;

enum AdCategory: string
{
    case SHILLERS = 'shillers';
    case ATTENDEE = 'attendee';
    case DOXXER = 'doxxer';
    case AUDITOR = 'auditor';
    case DEVELOPER = 'developer';
    case LISTING = 'listing';
    case HOST = 'host';
    case MODERATOR = 'moderator';
    case WRITING = 'writing';
    case INFLUENCER = 'influencer';
    case OTHER = 'other';

    public function tags(): array
    {
        return match ($this) {
            static::SHILLERS => ['telegram', 'twitter', 'discord', 'reddit'],
            static::ATTENDEE => ['video event', 'audio event', 'chat event'],
            static::DOXXER => ['kyc', 'video', 'signups', 'sms code'],
            static::AUDITOR => ['solidity', 'cairo', 'rust'],
            static::DEVELOPER => ['dapp', 'solidity', 'cairo', 'rust', 'full-stack'],
            static::LISTING => ['cex', 'cmc', 'gecko', 'fast track'],
            static::HOST => ['audio event', 'video event', 'co-host'],
            static::MODERATOR => ['telegram', 'twitter', 'discord'],
            static::WRITING => ['articles', 'distribution', 'documentation', 'tutorials'],
            static::INFLUENCER => ['give-away', 'x.com', 'instagram', 'telegram', 'video', 'gaming'],
            static::OTHER => [],
        };
    }

    public function descriptions()
    {
        return match ($this) {
            static::SHILLERS => 'Community Shillers',
            static::ATTENDEE => 'Attend online Event',
            static::DOXXER => 'Supply Authentic KYC info',
            static::AUDITOR => 'Create Contract Audit Report',
            static::DEVELOPER => 'Create contracts / daaps',
            static::LISTING => 'CEX /Exchange listing Service',
            static::HOST => 'Host online Event',
            static::MODERATOR => 'Moderate Community',
            static::WRITING => 'Writing Services',
            static::INFLUENCER => "Influencer Marketing",
            static::OTHER => "Any other Crypto Service",
        };
    }
}
