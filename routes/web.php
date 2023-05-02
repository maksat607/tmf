<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    $request_uri = $_SERVER['REQUEST_URI'];
    $host = $_SERVER['HTTP_HOST'];
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $full_url = $scheme . '://' . $host . $request_uri;
    $headers = getallheaders();
    dd(!isset($headers['Cf-Ipcountry']));
});
