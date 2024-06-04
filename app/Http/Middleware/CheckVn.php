<?php

namespace App\Http\Middleware;

use App\Models\InfluencerProject;
use Closure;
use App\Web3\AddressValidator;

class CheckVn
{

    public function handle($request, Closure $next)
    {

        if (!$request->query('vn'))
            return $next($request);
        $vn = $request->query('vn');
        $exists = InfluencerProject::where('vn', $vn)->exists();
        if (!$exists) return $next($request);
        return redirect($request->fullUrl())->withCookie(cookie()->forever('vn', $vn));
    }
}
