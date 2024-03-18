<?php

namespace App\Http\Middleware;

use Closure;
use Str;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && $request->user()->isAdmin()) {
            return $next($request);
        }
        return redirect()->route('home');
    }
}
