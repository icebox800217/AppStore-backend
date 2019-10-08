<?php

namespace App\Http\Middleware;

use Closure;

class DevelopMiddleware
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
        $level = Session::get('level');
        if (Session::has('name') && $level >= 2)
            return $next($request);
    }
}
