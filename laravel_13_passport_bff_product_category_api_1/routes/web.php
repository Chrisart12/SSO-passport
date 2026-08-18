<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return ['Laravel' => app()->version()];
});



// Route::get('/login', [LoginController::class, 'handleLogin']);
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/auth/callback', [LoginController::class, 'handleCallback']);

Route::get('/register', [RegisterController::class, 'register'])->name('register');
