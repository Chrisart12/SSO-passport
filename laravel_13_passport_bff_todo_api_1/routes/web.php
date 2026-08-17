<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


// Route::get('/login', [LoginController::class, 'handleLogin']);
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/auth/callback', [LoginController::class, 'handleCallback']);

Route::get('/register', [RegisterController::class, 'register'])->name('register');

