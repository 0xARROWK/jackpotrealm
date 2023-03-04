<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifyBans
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $id = null;
        if (Auth::user()) $id = DB::table('bans')->select('username')->where('user_id', '=', Auth::user()->id)->get()->first();
        $ip = DB::table('bans')->select('username')->where('ip', '=', $request->ip())->get()->first();
        if ($id === null && $ip === null) {
            return $next($request);
        } else {
            return response()->view('banned');
        }
    }
}
