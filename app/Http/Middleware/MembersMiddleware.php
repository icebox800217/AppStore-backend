<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
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
        if (Session::has('name') && Session::get('level')>=3
        //&& Session::get('sess') == Members::where(['id', '=', Session::get('id') ->select('sess')->get()])
        ){
            return $next($request);
        }
        else {
            return response()->json(["isSuccess" => "False"]);
    }
}
}
