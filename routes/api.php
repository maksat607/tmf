<?php

use Illuminate\Http\Request;
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

Route::group(['prefix' => 'tickets'], function ($router) {
    Route::resource('match-alert-rules', '\App\Http\Controllers\Ticket\MatchAlertRulesController');
    Route::resource('', '\App\Http\Controllers\Ticket\TicketController');
});


