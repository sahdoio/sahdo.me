<?php

namespace App\Http\Middleware;

use App\Libs\AdminUserSession;
use Closure;

class CheckAuthAdmin
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
        $my_session = new AdminUserSession();

        if (!$my_session->checkSession()) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }   
}
