<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    //    public function handle($request, Closure $next)
    //    {
    //        //$response = $next($request);
    //
    //        // $request->user() returns an instance of the authenticated user...
    //        if ( !$request->user() || !$request->user()->isAdmin()) {
    //            return redirect('/');
    //        }
    //
    ////        return $response;
    //        return $next($request);
    //    }

    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        return redirect('/');
    }
}
