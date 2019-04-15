<?php

namespace App\Http\Middleware;

use App\Libs\Session;
use Closure;

class SessionVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        return redirect()->route('building');
//
//        $session = new Session();
        return $next($request);
    }
}
