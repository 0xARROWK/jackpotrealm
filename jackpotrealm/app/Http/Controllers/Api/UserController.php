<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ZxcvbnPhp\Zxcvbn;
use App\Traits\ValidatorTrait;

class UserController extends Controller
{
    use ValidatorTrait;

    /**
     * Update user's information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $infos = DB::table('users')->select(['username', 'email', 'password'])->where('id', Auth::user()->id)->get()->first();
        $emailUpdated = false;

        if ($request->username && $request->username !== $infos->username) {
            $error = $this->validator($request->all(), ['username' => ['required', 'string', 'between:3,25', 'unique:users', 'regex:/^[_a-zA-Z0-9]{3,25}$/']]);
            if ($error === false) {
                $request->user()->fill(['username' => $request->username])->save();
            } else {
                return response()->json(['error' => $error]);
            }
        }

        if ($request->email && $request->email !== $infos->email) {
            $error = $this->validator($request->all(), ['email' => ['required', 'string', 'email', 'max:75', 'unique:users']]);
            if ($error === false) {
                DB::table('users')->where('id', Auth::user()->id)->update(['email' => $request->email, 'email_verified_at' => NULL]);
                $query = User::select(['email', 'id'])->where('id', Auth::user()->id)->first();
                $query->sendEmailVerificationNotification();
                $emailUpdated = true;
            } else {
                return response()->json(['error' => $error]);
            }
        }

        if ($request->password || $request->passwordConfirmation || $request->oldPassword) {
            if ($request->password && $request->password_confirmation && $request->old_password) {
                if (Hash::check($request->old_password, $infos->password)) {
                    if ($request->password !== $request->old_password) {
                        $error = $this->validator($request->all(), ['password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/']]);
                        if ($error === false) {
                            $zxcvbn = new Zxcvbn();
                            $strength = $zxcvbn->passwordStrength($request->password)['score'];
                            if (is_nan($strength) || $strength * 25 < 75) {
                                return response()->json(['error' => 'Strength.password']);
                            }
                            $request->user()->fill(['password' => Hash::make($request->password)])->save();
                        } else {
                            return response()->json(['error' => $error]);
                        }
                    } else {
                        return response()->json(['error' => 'You can\'t reuse your old password']);
                    }
                } else {
                    return response()->json(['error' => 'Wrong password']);
                }
            } else {
                return response()->json(['error' => 'To change your password, please enter the new password, the confirmation and your old password']);
            }
        }

        return response()->json(['success' => 'ok', 'email_updated' => $emailUpdated]);
    }

    public function updateProfilePicture(Request $request)
    {
        $file = $request->file('profile_picture');

        $error = $this->validator(['profile_picture' => $file], ['profile_picture' => ['mimes:jpeg,jpg,png,gif', 'required', 'max:5000']]);
        $path = public_path('storage/users/'.Auth::user()->username);

        if (!Storage::exists($path)) Storage::makeDirectory('public/users/'.Auth::user()->username);

        if ($error === false) {
            $url = Storage::putFileAs('public/users/'.Auth::user()->username, $file, 'profile_picture.jpg');
            return response()->json(['success' => $url]);
        } else {
            return response()->json(['error' => $error]);
        }
    }

    public function updateChatColor(Request $request)
    {
        if (in_array('sub-1', explode(',', Auth::user()->role), true) || in_array('sub-2', explode(',', Auth::user()->role), true)
            || in_array('sub-3', explode(',', Auth::user()->role), true) || in_array('streamer', explode(',', Auth::user()->role))) {
            DB::table('users')->where('id', Auth::user()->id)->update([
                'chat_color' => htmlspecialchars($request->input('color')),
                'updated_at' => new \DateTime()
            ]);
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => true]);
        }
    }
}
