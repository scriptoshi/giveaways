<?php
namespace App\Enums;
enum GiveawayType: string
{
	case DRAW = 'draw';
	case LEADERBOARD = 'leaderboard';
	case DRAW_LEADERBOARD = 'draw_leaderboard';
	case FCFS = 'fcfs';
	case FCFS_LEADERBOARD = 'fcfs_leaderboard';
	case DRAW_FCFS = 'draw_fcfs';

}
