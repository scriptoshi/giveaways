<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use App\Web3\AddressValidator;
class CheckReferral
{

    public function handle($request, Closure $next)
    {

        if (auth()->check() || $request->hasCookie('referral'))
        return $next($request);
        if(!$request->query('ref') && !$request->query('r'))
        return $next($request);
        $ref = $request->query('ref') ?? $request->query('r');
        $account = AddressValidator::getCanonicalAddress($ref);
        if (!$account) return $next($request);
        $exists = Account::addressExists($account);
        if (!$exists) return $next($request);
        return redirect($request->fullUrl())->withCookie(cookie()->forever('referral', $account));
    }
}
