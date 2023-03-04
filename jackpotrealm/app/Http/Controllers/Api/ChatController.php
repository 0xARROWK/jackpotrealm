<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BotCommands;
use App\Traits\ValidatorTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    use ValidatorTrait;

    public function uploadCustomEmoji(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $file = $request->file('custom_emoji');
            $name = htmlspecialchars($request->input('custom_emoji_name'));
            $tier = $request->input('tier');

            $error = $this->validator([
                'emoji' => $file,
                'emojiName' => $name
            ], [
                'emoji' => ['mimes:jpeg,jpg,png,gif', 'required', 'max:10000'],
                'emojiName' => ['required', 'string', 'between:3,25', 'regex:/[a-zA-Z0-9_]/']
            ]);
            $path = public_path('storage/users/' . Auth::user()->username . '/emojis/' . $tier);

            if (!Storage::exists($path)) Storage::makeDirectory('public/users/' . Auth::user()->username . '/emojis/' . $tier);
            if (file_exists(public_path('storage/users/' . Auth::user()->username . '/emojis/free/' . $name . '.png'))) $error = 'Unique.emojiName';
            if (file_exists(public_path('storage/users/' . Auth::user()->username . '/emojis/tier_1/' . $name . '.png'))) $error = 'Unique.emojiName';
            if (file_exists(public_path('storage/users/' . Auth::user()->username . '/emojis/tier_2/' . $name . '.png'))) $error = 'Unique.emojiName';
            if (file_exists(public_path('storage/users/' . Auth::user()->username . '/emojis/tier_3/' . $name . '.png'))) $error = 'Unique.emojiName';

            if ($error === false) {
                $url = Storage::putFileAs('public/users/' . Auth::user()->username . '/emojis/' . $tier, $file, $name . '.png');
                return response()->json(['success' => $url]);
            } else {
                return response()->json(['error' => $error]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    public function updateCustomEmoji(Request $request): JsonResponse
    {
        try {

            $tier = $request->input('newTier');
            $oldTier = $request->input('currentTier');
            $emojiName = $request->input('name');

            Storage::move('public/users/'.Auth::user()->username.'/emojis/'.$oldTier.'/'.$emojiName.'.png',
                'public/users/'.Auth::user()->username.'/emojis/'.$tier.'/'.$emojiName.'.png');

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function deleteCustomEmoji(Request $request): JsonResponse
    {
        try {
            $emoji = $request->input('emoji');
            $tier = $request->input('tier');
            $emojis = Storage::files('public/users/' . Auth::user()->username . '/emojis/' . $tier);
            for ($i = 0; $i < sizeof($emojis); $i++) $emojis[$i] = pathinfo($emojis[$i])['filename'];

            if (in_array($emoji, $emojis, true)) Storage::delete('public/users/' . Auth::user()->username . '/emojis/' . $tier . '/' . $emoji . '.png');

            return response()->json(['success' => in_array($emoji, $emojis, true)]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    public function getAllEmojis(Request $request): JsonResponse
    {
        $user = DB::table('users')->select('username')->where('role', 'streamer')->get()->first();

        $emojisFree = Storage::files('public/users/'.$user->username.'/emojis/free');
        for ($i = 0; $i < sizeof($emojisFree); $i++) $emojisFree[$i] = pathinfo($emojisFree[$i])['filename'];
        $emojisTier1 = Storage::files('public/users/'.$user->username.'/emojis/tier_1');
        for ($i = 0; $i < sizeof($emojisTier1); $i++) $emojisTier1[$i] = pathinfo($emojisTier1[$i])['filename'];
        $emojisTier2 = Storage::files('public/users/'.$user->username.'/emojis/tier_2');
        for ($i = 0; $i < sizeof($emojisTier2); $i++) $emojisTier2[$i] = pathinfo($emojisTier2[$i])['filename'];
        $emojisTier3 = Storage::files('public/users/'.$user->username.'/emojis/tier_3');
        for ($i = 0; $i < sizeof($emojisTier3); $i++) $emojisTier3[$i] = pathinfo($emojisTier3[$i])['filename'];

        return response()->json([
            'emojis' => [
                'free' => empty($emojisFree) ? null : $emojisFree,
                'tier_1' => empty($emojisTier1) ? null : $emojisTier1,
                'tier_2' => empty($emojisTier2) ? null : $emojisTier2,
                'tier_3' => empty($emojisTier3) ? null : $emojisTier3
            ]
        ]);
    }

    public function searchEmojis(Request $request): JsonResponse
    {
        $user = DB::table('users')->select('username')->where('role', 'streamer')->get()->first();
        $search = htmlspecialchars($request->input('search'));

        $resultsFree = $this->searchEmojisIn('free', $user, $search);
        $resultsTier1 = $this->searchEmojisIn('tier_1', $user, $search);
        $resultsTier2 = $this->searchEmojisIn('tier_2', $user, $search);
        $resultsTier3 = $this->searchEmojisIn('tier_3', $user, $search);

        return response()->json([
            'emojis' => [
                'free' => empty($resultsFree) ? null : $resultsFree,
                'tier_1' => empty($resultsTier1) ? null : $resultsTier1,
                'tier_2' => empty($resultsTier1) ? null : $resultsTier2,
                'tier_3' => empty($resultsTier1) ? null : $resultsTier3
            ]
        ]);
    }

    function searchEmojisIn(string $dir, $user, $search): array
    {
        $emojis = Storage::files('public/users/'.$user->username.'/emojis/'.$dir);
        for ($i = 0; $i < sizeof($emojis); $i++) $emojis[$i] = ':'.pathinfo($emojis[$i])['filename'].':';
        $results = [];
        for ($i = 0; $i < sizeof($emojis); $i++) if (str_contains($emojis[$i], $search)) $results[] = str_replace(':', '', $emojis[$i]);
        //if (sizeof($results) === 10) break;
        return $results;
    }

    public function showChatWidget()
    {
        $user = DB::table('users')->select('username')->where('role', 'streamer')->get()->first();
        return view('widgets/chat')
            ->with('streamer', $user->username);
    }

    public function getAvailableChatColors(): JsonResponse
    {
        $user = DB::table('users')->select('chat_color')->where('id', Auth::user()->id)->get()->first();
        return response()->json(['success' => [
            'current' => $user->chat_color,
            'others' => array('29c848', 'fb46e4', 'f09c36', 'cfae49', 'a2c5f1', '54bafa', 'b51264', 'c9085c')
        ]]);
    }

    public function addBotCommand(Request $request): JsonResponse
    {
        try {
            $commands = DB::table('bot_commands')->pluck('command')->toArray();
            if (!in_array($request->input('command'), $commands)) {
                BotCommands::create([
                    'user_id' => Auth::user()->id,
                    'command' => htmlspecialchars($request->input('command')),
                    'description' => htmlspecialchars($request->input('description')),
                    'response' => htmlspecialchars($request->input('response')),
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime()
                ]);
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => 'This command name is already used']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }

    public function editBotCommand(Request $request): JsonResponse
    {
        try {
            if (in_array($request->input('command'), ['ban', 'help'])) return response()->json(['error' => 'You can\'t modify this command']);
            DB::table('bot_commands')->where('command', '=', $request->input('oldCommand'))->update([
                'command' => htmlspecialchars($request->input('command')),
                'description' => htmlspecialchars($request->input('description')),
                'response' => htmlspecialchars($request->input('response')),
                'updated_at' => new \DateTime()
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred']);
        }
    }

    public function deleteBotCommand(Request $request): JsonResponse
    {
        try {
            if (in_array($request->input('command'), ['ban', 'help'])) return response()->json(['error' => 'You can\'t delete this command']);
            DB::table('bot_commands')->where('command', '=', $request->input('command'))->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }

    public function getBotCommands(): JsonResponse
    {
        try {
            $streamer = DB::table('users')->select('id')->where('role', '=', 'streamer')->get()->first();
            $botCommands = DB::table('bot_commands')->select(['command', 'description', 'response'])->where('user_id', '=', $streamer->id)->get()->toArray();

            foreach ($botCommands as $key => $value) {
                $value->toCreate = false;
                $value->edit = false;
            }
            return response()->json([
                'success' => empty($botCommands) ? null : $botCommands
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true]);
        }
    }
}
