<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccessController extends Controller
{
    /**
     * Allows to check multiple value of a user to manage access
     *
     * @param $request - array with all things to check
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        $access = true;

        if ($request->has('role')) {
            for ($i = 0; $i < sizeof($request->role); $i++) {
                $access = $this->checkRole($request->role);
                if (!$access) break;
            }
        }

        if ($request->has('verified')) $access = $this->checkVerified();

        return response()->json(['success' => $access]);
    }

    /**
     * Check if the user's role is really theirs
     *
     * @param $role - user's role on frontend
     * @return bool
     */
    public function checkRole($role)
    {
        $hasRole = in_array($role, explode(',', DB::table('users')->select('role')->where('id', Auth::user()->id)->get()->first()->role), true);

        return $hasRole;
    }

    /**
     * Allows to check if a user has verified his email address
     *
     * @return bool
     */
    public function checkVerified()
    {
        if (!Auth::user()->hasVerifiedEmail()) return false;
        else return true;
    }
}
