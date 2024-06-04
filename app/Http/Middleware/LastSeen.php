<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Auth;

class LastSeen
{
    public function handle($request, Closure $next)
    {

        if (Auth::check()) {
            // update every 5 minutes
            if (is_null($request->user()->last_seen_at) || $request->user()->last_seen_at->addMinutes(15)->lt(now())) {
                $request->user()->update(['last_seen_at' => now()]);
            }
        }
        return $next($request);
    }
}
