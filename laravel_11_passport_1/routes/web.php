<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\OauthLogoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/oauth/logout', OauthLogoutController::class);
// Route::middleware('auth')->get('/clear-cache', function () {
//     Artisan::call('optimize:clear');
//     return back()->with('status', 'Application cache cleared.');
// })->name('cache.clear');

require __DIR__.'/auth.php';
