<?php

namespace App\Http\Middleware;

use App\Libs\UserSession;
use Closure;

class CheckAuth
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
        /*
         * Pendente de implementação
         *

        $my_session = new UserSession();

        if (!$my_session->checkSession()) {
            return redirect()->route('website.login');
        }

        */

        return $next($request);
    }   
}
