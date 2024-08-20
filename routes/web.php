<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/phpinfo', function () {
    return phpinfo();
});
Route::get('/logout', function () {
    Auth::logout();
});
