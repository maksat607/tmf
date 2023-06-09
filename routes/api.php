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
});
Route::group(['middleware' => 'auth.access_token', 'prefix' => 'auth'], function ($router) {
    Route::delete('logout', [\App\Http\Controllers\Auth\UserController::class, 'destroy']);
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



