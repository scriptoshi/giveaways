<?php

namespace App\Enums;

enum Period: string
{
    case HOURS = 'hours';
    case DAYS = 'days';
    case WEEKS = 'weeks';
    case MONTHS = 'months';
}
