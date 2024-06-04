<?php

namespace App\Enums;

enum ContributionStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case ZERO = 'zero';
    case VERIFIED = 'verified';
    case MISMATCH = 'mismatch';

    public function status()
    {
        match ($this) {
            static::PENDING => __('Transaction is queued for verification'),
            static::REJECTED => __("Transaction was rejected by the network"),
            static::ZERO => __("Transaction amount is Zero"),
            static::MISMATCH => __("Transaction sender is not among your connected accounts"),
            static::VERIFIED => __("Transaction was verified successfully"),
        };
    }
}
