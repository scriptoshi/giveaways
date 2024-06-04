<?php
namespace App\Enums;
enum TaskStatus: string
{
	case PENDING = 'pending';
	case RUNNING = 'running';
	case FAILED = 'failed';
	case COMPLETE = 'complete';
	case SKIP = 'skip';

}
