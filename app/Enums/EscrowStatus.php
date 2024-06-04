<?php
namespace App\Enums;
enum EscrowStatus: string
{
	case PENDING = 'pending';
	case RELEASED = 'released';
	case CLAIMED = 'claimed';
	case LOCKED = 'locked';
	case DISPUTE = 'dispute';
	case RESOLVED = 'resolved';

}
