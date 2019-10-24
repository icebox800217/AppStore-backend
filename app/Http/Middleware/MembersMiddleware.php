<?php

namespace App\Http\Middleware;

use Closure;

class MembersMiddleware
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
        if (Session::has('name') && $level >= 1)
            return $next($request);
    }
}
