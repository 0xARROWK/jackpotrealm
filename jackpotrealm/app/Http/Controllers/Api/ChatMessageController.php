<?php

namespace App\Http\Controllers\Api;

use App\Events\DeleteChatMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Events\NewChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jojoee\Library\LeoProfanity;

class ChatMessageController extends Controller
{
    public function broadcast(Request $request): \Illuminate\Http\JsonResponse
    {

        if (!Auth::check() || !Auth::user()->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'You must be logged in and have verified your email address to send messages in the chat'
            ], 401);
        } else if (!$request->filled('id') || !$request->filled('user') || !$request->filled('text') || !$request->filled('color')) {
            return response()->json([
                'error' => 'Invalid data'
            ], 422);
        }

        try {
            $text = htmlentities($request->text);
            $moderate = false;
            $streamer = DB::table('users')->select(['id', 'username'])->where('role', 'like', '%streamer%')->get()->first();
            $banned = DB::table('moderations')->select(['banned_words', 'banned_expressions'])->where('user_id', $streamer->id)->get()->first();
            foreach (explode(' ', $text) as $word) {
                if (($banned->banned_words && in_array($word, explode("\n", $banned->banned_words))) || ($banned->banned_expressions && strpos($word, $banned->banned_expressions) !== false)) {
                    $moderate = true;
                    break;
                }
            }

            /*
             * $moderate = !empty(array_intersect(explode(' ', $text), explode(' ', $banned->banned_words)));
             */

            /*
             * $moderate = false;
             * $filter = new LeoProfanity();
             * if ($filter->check(strip_tags($text))) $moderate = true;
             */

            $emojisFree = Storage::files('public/users/' . $streamer->username . '/emojis/free');
            $emojisTier1 = Storage::files('public/users/' . $streamer->username . '/emojis/tier_1');
            $emojisTier2 = Storage::files('public/users/' . $streamer->username . '/emojis/tier_2');
            $emojisTier3 = Storage::files('public/users/' . $streamer->username . '/emojis/tier_3');
            $emojis = [$emojisFree, $emojisTier1, $emojisTier2, $emojisTier3];
            for ($i = 0; $i < sizeof($emojis); $i++) for ($j = 0; $j < sizeof($emojis[$i]); $j++) $emojis[$i][$j] = pathinfo($emojis[$i][$j])['filename'];
            if (preg_match('/(:[a-zA-Z0-9_]{3,25}:)/', $text)) {
                $text = preg_replace_callback('/(:[a-zA-Z0-9_]{3,25}:)/',
                    function ($match) use ($emojis) {
                        $emoji = str_replace(':', '', $match[1]);
                        $res = $emoji;
                        if (in_array($emoji, $emojis[0])) $res = ':free*' . $emoji . ':';
                        if (in_array($emoji, $emojis[1]) && in_array('tier_1', explode(',', Auth::user()->role))) $res = ':tier_1*' . $emoji . ':';
                        if (in_array($emoji, $emojis[2]) && in_array('tier_2', explode(',', Auth::user()->role))) $res = ':tier_2*' . $emoji . ':';
                        if (in_array($emoji, $emojis[3]) && in_array('tier_3', explode(',', Auth::user()->role))) $res = ':tier_3*' . $emoji . ':';
                        return $res;
                    },
                    $text);
            }

            $botMessage = false;
            if (str_starts_with($text, "!") && sizeof(explode(" ", $text)) < 5) {
                $command = str_replace('!', '', explode(" ", $text)[0]);
                $commandsArray = DB::table('bot_commands')->pluck('command')->toArray();
                $commandsAll = DB::table('bot_commands')->select(['command', 'description'])->get();
                if (count(array_intersect([$command], ['ban'])) !== 0 && count(array_intersect(['moderator', 'streamer'], explode(',', Auth::user()->role))) !== 0) {
                    $botMessage = true;
                } else if (count(array_intersect([$command], ['help'])) !== 0) {
                    $text = '<br><span class="text-white">!help</span> : display this help.<br>';
                    foreach ($commandsAll as $c) {
                        $text .= '<span class="text-white">!'.$c->command.'</span> : '.$c->description.'<br>';
                    }
                    $botMessage = true;
                } else if (in_array($command, $commandsArray)) {
                    $text = DB::table('bot_commands')->select('response')->where('command', '=', $command)->get()->first()->response;
                    $botMessage = true;
                }
            }

            if ($botMessage) {
                event(new NewChatMessage(0, "ChatBot", $text, "00a1e8", ['bot'], 0));
            } else if (!$moderate && Auth::user()->id === $request->id && Auth::user()->username === $request->user && Auth::user()->chat_color === $request->color) {
                event(new NewChatMessage(htmlentities($request->id), htmlentities($request->user), $text, htmlentities($request->color), explode(',', Auth::user()->role), htmlentities($request->mid)));
            }

            return response()->json(['moderate' => $moderate, 'message' => $text]);
        } catch (\Exception $e) {
            return response()->json(['error' => true], 503);
        }

    }

    public function deleteMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $id = htmlentities($request->input('mid'));
            event(new DeleteChatMessage($id));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => true], 503);
        }
    }

}
