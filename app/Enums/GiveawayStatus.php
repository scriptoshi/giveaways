<?php

namespace App\Enums;

enum GiveawayStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case ERROR = 'error';
}
