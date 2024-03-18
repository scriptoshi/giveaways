<?php
namespace App\Enums;
enum LaunchpadUnsold_tokens: string
{
	case REFUND = 'refund';
	case BURN = 'burn';
	case LOCK = 'lock';
	case STAKING = 'staking';
	case LIQUIDITY_STAKING = 'liquidity_staking';
	case AIRDROP = 'airdrop';
	case DEXEYE_LIQUIDITY = 'dexeye_liquidity';

}
