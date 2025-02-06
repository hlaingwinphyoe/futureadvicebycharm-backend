<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FrontController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// blogs
Route::controller(PostController::class)->group(function () {
    Route::get('/posts-list', 'getPosts');
    Route::get('/posts-list/{slug}', 'getPost');
    Route::get('/posts-list/special/today', 'getTodaySpecial');
    Route::get('/posts-list/{id}/recent', 'getRecentPost');
});

Route::controller(FrontController::class)->group(function () {
    // zodiac
    Route::get('/zodiacs-list', 'getZodiacs');
    Route::get('/packages-list', 'getPackages');
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
