<?php

namespace App\Actions\Task;

use App\Models\Task;
use Illuminate\Http\Request;

class Verifier
{
    public function verify(Task $task): bool
    {
        return false;
    }
}
