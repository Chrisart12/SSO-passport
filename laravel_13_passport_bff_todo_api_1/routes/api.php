<?php

// use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::prefix('v1')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

    // Route::get('/login', [LoginController::class, 'login']);

    Route::middleware(['auth:sanctum', 'oauth.fresh'])->group(function () {

    Route::apiResource('posts', PostController::class);
    Route::apiResource('posts.comments', CommentController::class);
    Route::apiResource('tasks', TaskController::class);
    
    });

});

