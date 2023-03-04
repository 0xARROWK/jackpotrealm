<?php

namespace App\Http\Controllers\Api;

use App\Mail\Banned;
use App\Mail\Unbanned;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ModerationController extends Controller
{
    public function addBannedWordsOrExpressions(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $action = $request->input('action');
            if ($action === 0 && $request->exists('words')) {

                DB::table('moderations')->where('user_id', Auth::user()->id)->update([
                    'banned_words' => $request->input('words'),
                    'updated_at' => new \DateTime()
                ]);

                return response()->json(['success' => true]);

            } else if ($action === 1 && $request->exists('expressions')) {

                DB::table('moderations')->where('user_id', Auth::user()->id)->update([
                    'banned_expressions' => $request->input('expressions'),
                    'updated_at' => new \DateTime()
                ]);

                return response()->json(['success' => true]);

            } else {
                return response()->json(['error' => 'Bad action requested']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Your data could not be saved']);
        }
    }

    public function getBannedWordsOrExpressions(Request $request, $action): \Illuminate\Http\JsonResponse
    {
        try {
            $banned = DB::table('moderations')->select(['banned_words', 'banned_expressions'])->where('user_id', Auth::user()->id)->get()->first();
            return response()->json(['success' => [
                'words' => ($action === '0' || $action === '2') ? $banned->banned_words : null,
                'expressions' => ($action === '1' || $action === '2') ? $banned->banned_expressions : null
            ]]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    public function addModerator(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $username = htmlspecialchars($request->input('name'));
            $roles = DB::table('users')->select('role')->where('username', '=', $username)->get()->first();
            if (!empty($roles)) {
                if (strpos($roles->role, "moderator") === false) {
                    DB::table('users')->where('username', '=', $username)->update([
                        'role' => $roles->role . ',moderator',
                        'updated_at' => new \DateTime()
                    ]);
                } else {
                    return response()->json(['error' => 'This user is already a moderator']);
                }
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => 'This user does not exist']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }

    public function deleteModerator(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $username = htmlspecialchars($request->input('name'));
            $roles = DB::table('users')->select('role')->where('username', '=', $username)->get()->first();
            DB::table('users')->where('username', '=', $username)->update([
                'role' => str_replace(',moderator', '',$roles->role),
                'updated_at' => new \DateTime()
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }

    public function getModerators(): \Illuminate\Http\JsonResponse
    {
        try {
            $m = DB::table('users')->select('username')->where('role', 'like', '%moderator%')->get()->toArray();
            return response()->json(['success' => empty($m) ? null : $m]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }

    public function addBannedUser(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $username = htmlspecialchars($request->input('name'));
            $user = DB::table('users')->select(['username', 'id'])->where('username', '=', $username)->get()->first();
            if (!empty($user)) {
                $alreadyBanned = DB::table('bans')->select('username')->where('username', '=', $username)->get()->first();
                if (empty($alreadyBanned)) {
                    DB::table('bans')->insert([
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'ip' => 'none',
                        'created_at' => new \Datetime(),
                        'updated_at' => new \DateTime()
                    ]);
                    Mail::to(DB::table('users')->where('username', '=', $username)->get()->first())->send(new Banned($username));
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['error' => 'This user is already banned']);
                }
            } else {
                return response()->json(['error' => 'This user does not exist']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }

    public function deleteBannedUser(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $username = htmlspecialchars($request->input('name'));
            DB::table('bans')->where('username', '=', $username)->delete();
            Mail::to(DB::table('users')->where('username', '=', $username)->get()->first())->send(new Unbanned($username));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }

    public function getBannedUsers(): \Illuminate\Http\JsonResponse
    {
        try {
            $m = DB::table('bans')->select('username')->get()->toArray();
            return response()->json(['success' => empty($m) ? null : $m]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }
}
