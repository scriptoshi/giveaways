<?php

namespace App\Enums;

enum LaunchpadStatus: string
{

    case QUEUED = 'queued';
    case PENDING = 'pending';
    case LIVE = 'live';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case COMPLETE = 'complete';
    case ENDED = 'ended';
    case EXTERNAL = 'external';
 
    public function statusCode(): string
    {
        return match ($this) {
            LaunchpadStatus::QUEUED, LaunchpadStatus::PENDING =>  0,
            LaunchpadStatus::LIVE => 1,
            LaunchpadStatus::COMPLETE, LaunchpadStatus::SUCCESS => 2,
            LaunchpadStatus::FAILED, LaunchpadStatus::CANCELLED => 3,
        };
    }

    
}
