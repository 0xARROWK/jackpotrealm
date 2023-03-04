<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Public api routes without session requirements
 */

// account registration and edition verifications
Route::prefix('verification')->group(function() {

    Route::get('/username/{username}', 'App\Http\Controllers\Auth\RegisterController@checkIfUsernameExist')
        ->where('username', '^[_a-zA-Z0-9]{3,25}$');
    Route::get('/email/{email}', 'App\Http\Controllers\Auth\RegisterController@checkIfEmailExist')
        ->where('email', '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$');
    Route::post('/password', 'App\Http\Controllers\Auth\RegisterController@checkPasswordStrength');

});

Route::prefix('channel')->group(function() {

    Route::get('/infos', '\App\Http\Controllers\Api\StreamController@getStreamInfos');

});

Route::prefix('stream')->group(function() {

    Route::post('/', '\App\Http\Controllers\Api\StreamController@startStream');
    Route::post('/end', '\App\Http\Controllers\Api\StreamController@endStream');

});

Route::prefix('widgets')->group(function() {

    Route::get('/chat', '\App\Http\Controllers\Api\ChatController@showChatWidget');

});



/**
 * Public api routes with session requirements
 */
Route::middleware('api-session')->group(function() {

    // language handler
    Route::prefix('languages')->group(function() {

        Route::get('list','\App\Http\Controllers\Api\LanguageController@availableLanguagesCodeAndLanguage');
        Route::get('available', '\App\Http\Controllers\Api\LanguageController@availableLanguagesCode');
        Route::get('default','\App\Http\Controllers\Api\LanguageController@defaultLanguage');
        Route::post('modify/{locale}', 'App\Http\Controllers\Api\LanguageController@changeLanguage')->where('locale', '[a-z]{2}_[A-Z]{2}');

    });

});



/**
 * Protected api with session requirements
 */
Route::middleware(['auth:api', 'scopes:user-data'])->group(function() {

    // verifications
    Route::prefix('/verification')->group(function() {

        Route::post('/access', 'App\Http\Controllers\Api\AccessController@check');

    });

    // user data
    Route::prefix('/user')->group(function() {

        Route::get('/', function (Request $request) {
            return $request->user();
        });

        Route::prefix('/account-settings')->group(function() {

            Route::get('/chat-color', 'App\Http\Controllers\Api\ChatController@getAvailableChatColors');

            Route::prefix('/update')->group(function() {

                Route::post('/', 'App\Http\Controllers\Api\UserController@update');
                Route::post('/picture', 'App\Http\Controllers\Api\UserController@updateProfilePicture');
                Route::post('/chat-color', 'App\Http\Controllers\Api\UserController@updateChatColor');

            });

        });

        Route::prefix('/moderation')->group(function() {

            Route::get('/expressions/{action}', 'App\Http\Controllers\Api\ModerationController@getBannedWordsOrExpressions')
                ->where('action', '^[0|1|2]$');
            Route::get('/get-moderators', 'App\Http\Controllers\Api\ModerationController@getModerators');
            Route::get('/get-banned-users', 'App\Http\Controllers\Api\ModerationController@getBannedUsers');

            Route::post('/expressions', 'App\Http\Controllers\Api\ModerationController@addBannedWordsOrExpressions');
            Route::post('/add-moderator', 'App\Http\Controllers\Api\ModerationController@addModerator');
            Route::post('/delete-moderator', 'App\Http\Controllers\Api\ModerationController@deleteModerator');
            Route::post('/add-banned-user', 'App\Http\Controllers\Api\ModerationController@addBannedUser');
            Route::post('/delete-banned-user', 'App\Http\Controllers\Api\ModerationController@deleteBannedUser');

        });

        Route::prefix('/dashboard')->group(function() {

            Route::get('/sk', 'App\Http\Controllers\Api\StreamController@getSK');
            Route::get('/multistream-infos', 'App\Http\Controllers\Api\StreamController@getMultistreamInfos');

            Route::post('/sk/regenerate', 'App\Http\Controllers\Api\StreamController@regenerateSK');
            Route::post('/new-emoji', 'App\Http\Controllers\Api\ChatController@uploadCustomEmoji');
            Route::post('/update-emoji', 'App\Http\Controllers\Api\ChatController@updateCustomEmoji');
            Route::post('/delete-emoji', 'App\Http\Controllers\Api\ChatController@deleteCustomEmoji');
            Route::post('/stream-infos', 'App\Http\Controllers\Api\StreamController@setStreamInfos');
            Route::post('/multistream-infos', 'App\Http\Controllers\Api\StreamController@setMultistreamInfos');
            Route::post('/add-command', 'App\Http\Controllers\Api\ChatController@addBotCommand');
            Route::post('/edit-command', 'App\Http\Controllers\Api\ChatController@editBotCommand');
            Route::post('/delete-command', 'App\Http\Controllers\Api\ChatController@deleteBotCommand');

        });

    });

    Route::prefix('/chat')->group(function() {

        Route::get('/list-emojis', 'App\Http\Controllers\Api\ChatController@getAllEmojis');
        Route::get('/list-commands', 'App\Http\Controllers\Api\ChatController@getBotCommands');

        Route::post('/send-message', 'App\Http\Controllers\Api\ChatMessageController@broadcast');
        Route::post('/search-emojis', 'App\Http\Controllers\Api\ChatController@searchEmojis');
        Route::post('/delete-message', 'App\Http\Controllers\Api\ChatMessageController@deleteMessage');

    });

});



/**
 * Protected api routes without session requirements
 */

// users data
/*Route::middleware(['auth:api', 'scopes:user-data'])->group(function() {

    Route::prefix('/user')->group(function() {

        Route::get('/', function (Request $request) {
            return $request->user();
        });

        Route::get('/role/{role}', 'App\Http\Controllers\Api\RoleController@checkRole')
            ->where('role', '^[a-z]{1,25}$');

    });

    Route::post('/message', 'App\Http\Controllers\Api\ChatMessageController@broadcast');

});*/
