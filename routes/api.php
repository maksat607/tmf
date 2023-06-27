<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/auth/login-by-firebase', [\App\Http\Controllers\Auth\UserController::class, 'loginByFirebase']);
Route::group(['middleware' => 'auth.access_token', 'prefix' => 'auth/users'], function ($router) {
    Route::get('me', [\App\Http\Controllers\Auth\UserController::class, 'index']);
    Route::put('me', [\App\Http\Controllers\Auth\UserController::class, 'update']);
    Route::get('{user}', [\App\Http\Controllers\Auth\UserController::class, 'show']);
    Route::post('{destination}/increase', [\App\Http\Controllers\Auth\UserController::class, 'likeAction']);
    Route::post('{destination}/decrease', [\App\Http\Controllers\Auth\UserController::class, 'decreaseAction']);
});
Route::group(['middleware' => 'auth.access_token', 'prefix' => 'auth'], function ($router) {
    Route::delete('logout', [\App\Http\Controllers\Auth\UserController::class, 'destroy']);
});
Route::group(['middleware' => 'auth.access_token', 'prefix' => 'chats'], function ($router) {
    Route::delete('logout', [\App\Http\Controllers\Auth\UserController::class, 'destroy']);
});
Route::group(['middleware' => 'auth.access_token', 'prefix' => 'dictionaries'], function ($router) {
    Route::get('airports', [\App\Http\Controllers\DictionaryController::class, 'airports']);
    Route::get('airlines', [\App\Http\Controllers\DictionaryController::class, 'airlines']);
    Route::get('currencies', [\App\Http\Controllers\DictionaryController::class, 'currencies']);
});
Route::group(['middleware' => 'auth.access_token', 'prefix' => 'chats'], function ($router) {
    Route::get('my', [\App\Http\Controllers\Chat\ChatController::class, 'my']);
    Route::get('{chat}', [\App\Http\Controllers\Chat\ChatController::class, 'show']);
    Route::put('{chat}/mark-unread-as-read', [\App\Http\Controllers\Chat\ChatController::class, 'makeRead']);
    Route::get('ticket/{ticket}', [\App\Http\Controllers\Chat\ChatController::class, 'ticketChats']);
    Route::get('ticket/{ticket}/reply-user/{replyUser}', [\App\Http\Controllers\Chat\ChatController::class, 'detailsByTicketAndReplyUser']);
    Route::post('/', [\App\Http\Controllers\Chat\ChatController::class, 'createChat']);
    Route::get('{chat}/messages', [\App\Http\Controllers\Chat\ChatController::class, 'messages']);
    Route::post('{chat}/messages', [\App\Http\Controllers\Chat\ChatController::class, 'createChatMessage']);


});
Route::group(['middleware' => 'auth.access_token', 'prefix' => 'tickets'], function ($router) {
    Route::get('favorites', [\App\Http\Controllers\Ticket\FavoriteTicketController::class, 'index']);
    Route::put('{ticket}/favorite', [\App\Http\Controllers\Ticket\FavoriteTicketController::class, 'store']);
    Route::delete('{ticket}/favorite', [\App\Http\Controllers\Ticket\FavoriteTicketController::class, 'destroy']);
    Route::get('{ticket}/favorite', [\App\Http\Controllers\Ticket\FavoriteTicketController::class, 'show']);

    Route::get('my', ['\App\Http\Controllers\Ticket\TicketController', 'mylist']);
    Route::post('{ticket}/mark-as-sold', ['\App\Http\Controllers\Ticket\TicketController', 'sold']);
});
Route::group(['prefix' => 'tickets'], function ($router) {
    Route::resource('match-alert-rules', '\App\Http\Controllers\Ticket\MatchAlertRulesController');
    Route::post('{id}/up-top-position', ['\App\Http\Controllers\Ticket\TicketController', 'upTopPosition']);
});
Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('notification-settings', ['\App\Http\Controllers\Auth\\NotificationSettingsController', 'update']);
});
Route::resource('tickets', '\App\Http\Controllers\Ticket\TicketController');



