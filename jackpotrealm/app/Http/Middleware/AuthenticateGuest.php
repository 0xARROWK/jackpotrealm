<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthenticateGuest
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
        if (!Auth::check()) {
            $guestTime = 'guest.'.microtime(true);
            $user = User::create([
                'username' => $guestTime,
                'email' => 'guest@guest.'.$guestTime,
                'birthdate' => substr($guestTime, 0, 10),
                'password' => Hash::make($guestTime),
                'chat_color' => '000000',
                'role' => 'guest',
                'token' => 'none'
            ]);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            DB::table('users')->where('username', $user->username)->delete();
        } else {
            $user = Auth::user();
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
