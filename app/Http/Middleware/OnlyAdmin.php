<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class OnlyAdmin
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
//        $user = Auth::user();
//
//        if ($user) {
//            if ($user->level == User::ADMIN) {
//                return $next($request);
//            }
//        }

        return redirect()->route('admin.dashboard');
    }
}
