<?php

namespace App\Http\Controllers\Api;

use App\Events\NewStreamStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Traits\StreamKeyTrait;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class StreamController extends Controller
{
    use StreamKeyTrait;

    public function startStream(Request $request)
    {
        $name = $request->input('name');

        $str = explode('_', $name)[1];
        $n = preg_replace('/[0-9]*[a-zA-Z]{1}/', '', $str);
        $user = DB::table('users')->select(['username'])->where('id', $n)->get()->first();
        $stream = DB::table('streams')->select(['key'])->where('user_id', $n)->get()->first();
        $multistream = DB::table('multistreams')->select('status')->where('user_id', $n)->get()->first();

        if ($name === $stream->key) {

            if ($multistream->status === 1) {
                $descriptorspec = array(
                    0 => array("pipe", "r"),
                    1 => array("pipe", "w"),
                    2 => array("file", "/var/log/multistreams_error.txt", "a")
                );
                $process = proc_open('sh /var/www/jackpotrealm.com/jackpotrealm/stream.sh', $descriptorspec, $pipes);
                /*$process = new Process(['sh /test_stream.sh']);
                $process->start();
                $pid = $process->getPid();
                $command = 'sh /test_stream.sh';/* > /var/log/multistreams_error.txt 2>&1 & echo $!;';*/
                $pid = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                DB::table('multistreams')->where('user_id', $n)->update([
                    'pid' => $pid
                ]);
            }

            DB::table('streams')->where('user_id', $n)->update([
                'status' => 1
            ]);

            header('Location: rtmp://127.0.0.1:1936/app/' . $user->username);
            // http_response_code(201);
        } else {
            http_response_code(404);
            // return response()->json(['error']);
        }
    }

    public function endStream(Request $request)
    {
        $addr = $request->input('addr');
        if ($addr !== '127.0.0.1:1937/twitch/') {
            $name = $request->input('name');

            $str = explode('_', $name)[1];
            $n = preg_replace('/[0-9]*[a-zA-Z]{1}/', '', $str);
            $multistream = DB::table('multistreams')->select(['status', 'pid'])->where('user_id', $n)->get()->first();

            if ($multistream->status === 1) {
                exec('kill -9 ' . $multistream->pid);
                DB::table('multistreams')->where('user_id', $n)->update([
                    'pid' => null
                ]);
            }

            DB::table('streams')->where('user_id', $n)->update([
                'status' => 0
            ]);

            event(new NewStreamStatus(0));
        }
    }

    public function getStreamInfos(): \Illuminate\Http\JsonResponse
    {
        try {

            $user = DB::table('users')->select(['username', 'id'])->where('role', 'like', '%streamer%')->get()->first();
            $stream = DB::table('streams')->select(['title', 'description', 'status'])->where('user_id', $user->id)->get()->first();
            return response()->json(['success' => [
                'channelName' => $user->username,
                'streamTitle' => $stream->title,
                'streamDescription' => $stream->description,
                'streamStatus' => $stream->status
            ]]);

        } catch (\Exception $e) {

            return response()->json(['error' => true]);

        }
    }

    public function setStreamInfos(Request $request): \Illuminate\Http\JsonResponse
    {
        $title = substr($request->input('title'), 0, 120);
        $description = substr($request->input('description'), 0, 2500);

        DB::table('streams')->where('user_id', Auth::user()->id)->update([
            'title' => $title,
            'description' => $description,
            'updated_at' => new \DateTime()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Allows to return the stream key of the logged-in user
     */
    public function getSK(): \Illuminate\Http\JsonResponse
    {
        $sk = $this->getStreamKey();

        return $sk !== null ? response()->json(['success' => $sk]) : response()->json(['error' => 'We are unable to retrieve your data']);
    }

    /**
     * Allows to regenerate the stream key of the logged-in user
     */
    public function regenerateSK(): \Illuminate\Http\JsonResponse
    {
        try {

            DB::table('streams')->where('user_id', Auth::user()->id)->update([
                'key' => $this->generateStreamKey(),
                'updated_at' => new \DateTime()
            ]);

            return response()->json(['success' => 'ok']);

        } catch (\Exception $e) {

            return response()->json(['error' => 'Your data could not be saved']);

        }
    }

    public function getMultistreamInfos(Request $request)
    {
        $multistreams = DB::table('multistreams')->select(['url', 'key', 'status'])->where('user_id', Auth::user()->id)->get()->first();

        return response()->json(['success' => [
            'url' => $multistreams->url,
            'key' => $multistreams->key,
            'status' => $multistreams->status
        ]]);
    }

    public function setMultistreamInfos(Request $request)
    {
        if ($request->input('action') === 'changeData') {
            $url = htmlspecialchars(substr($request->input('url'), 0, 100));
            $key = htmlspecialchars(substr($request->input('key'), 0, 100));
            if (!str_ends_with($url, '/')) $url = $url.'/';
            DB::table('multistreams')->where('user_id', Auth::user()->id)->update([
                'url' => $url,
                'key' => $key,
                'updated_at' => new \DateTime()
            ]);
            exec('echo \'ffmpeg -listen 1 -i rtmp://127.0.0.1:1937/twitch/ -c copy -f flv '.$url.$key.' > /var/log/multistreams_error.txt 2>&1 & echo $!;\' > /var/www/jackpotrealm.com/jackpotrealm/stream.sh');
            $stream = DB::table('streams')->select('status')->where('user_id', Auth::user()->id)->get()->first();
            $multistream = DB::table('multistreams')->select(['status', 'pid'])->where('user_id', Auth::user()->id)->get()->first();
            if ($multistream->status === 1 && $stream->status === 1) {
		        exec('kill -9 '.$multistream->pid);
		        $descriptorspec = array(
                    0 => array("pipe", "r"),
                    1 => array("pipe", "w"),
                    2 => array("file", "/var/log/multistreams_error.txt", "a")
                );
                $process = proc_open('sh /var/www/jackpotrealm.com/jackpotrealm/stream.sh', $descriptorspec, $pipes);
                $pid = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                DB::table('multistreams')->where('user_id', Auth::user()->id)->update([
                    'pid' => $pid
                ]);
            }
            return response()->json(['success' => true]);
        } else if ($request->input('action') === 'changeStatus') {
            $stream = DB::table('streams')->select('status')->where('user_id', Auth::user()->id)->get()->first();
            $multistream = DB::table('multistreams')->select('pid')->where('user_id', Auth::user()->id)->get()->first();
            $status = htmlspecialchars($request->input('status'));
            if ($status === '1' && $stream->status === 1) {
                $descriptorspec = array(
                    0 => array("pipe", "r"),
                    1 => array("pipe", "w"),
                    2 => array("file", "/var/log/multistreams_error.txt", "a")
                );
                $process = proc_open('sh /var/www/jackpotrealm.com/jackpotrealm/stream.sh', $descriptorspec, $pipes);
                $pid = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                DB::table('multistreams')->where('user_id', Auth::user()->id)->update([
                    'pid' => $pid
                ]);
            } else if ($status === '0' && $stream->status === 1) {
		        exec('kill -9 '.$multistream->pid);
		        DB::table('multistreams')->where('user_id', Auth::user()->id)->update([
	                'pid' => null
                ]);
            }
            DB::table('multistreams')->where('user_id', Auth::user()->id)->update([
                'status' => $status,
                'updated_at' => new \DateTime()
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => true]);
    }
}
