<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class UserAndAdmin
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
//        $parameters = $request->route()->parameters();
//        $user = Auth::user();
//
//        if ($user && isset($parameters['id'])) {
//            if ($user->level == User::ADMIN || $parameters['id'] == $user->id) {
//                return $next($request);
//            }
//        }

        return redirect()->route('admin.dashboard');
    }
}
