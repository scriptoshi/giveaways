<?php

namespace App\Enums;

enum QuestStatus: string
{
    case PENDING = 'pending';
    case DRAFT = 'draft';
    case ACTIVE = 'active';

    function isDraft()
    {
        return $this == ActionStatus::DRAFT;
    }
}
