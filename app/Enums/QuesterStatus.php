<?php
namespace App\Enums;
enum QuesterStatus: string
{
	case PENDING = 'pending';
	case REJECTED = 'rejected';
	case COMPLETED = 'completed';
	case DRAWN = 'drawn';
	case WINNER = 'winner';
	case CLAIMED = 'claimed';

}
