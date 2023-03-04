<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait StreamKeyTrait
{
    /**
     * Allows to retrieve the streaming key of the logged-in user
     */
    public function getStreamKey()
    {
        return DB::table('streams')->select('key')->where('user_id', Auth::user()->id)->get()->first();
    }

    /**
     * Allows to generate a stream key for the logged-in user
     *
     * @return string
     */
    public function generateStreamKey(): string
    {
        $id = Auth::user()->id;

        $figures = '0123456789';
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $all = $figures.$letters;
        $lgFigures = strlen($figures);
        $lgLetters = strlen($letters);
        $lgAll = strlen($all);

        $idLength = strlen($id);
        $longueur = 10 - $idLength;
        $k1 = '';
        for ($i = 0; $i < $longueur; $i++) {
            $k1 .= $figures[rand(0, $lgFigures - 1)];
        }
        $k1 .= $letters[rand(0, $lgLetters - 1)].$id;

        $k2 = '';
        $longueur = 50;
        for ($i = 0; $i < $longueur; $i++) {
            $k2 .= $all[rand(0, $lgAll - 1)];
        };

        return 'channel_'.$k1.'_'.$k2;
    }
}

?>
