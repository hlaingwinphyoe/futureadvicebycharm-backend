<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiFrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::controller(ApiFrontController::class)->group(function () {
        // blogs
        Route::get('/posts-list', 'getPosts');
        Route::get('/posts-list/{slug}', 'getPost');
        Route::get('/today-special', 'getTodaySpecial');

        // zodiac
        Route::get('/zodiacs-list', 'getZodiacs');
    });
});
