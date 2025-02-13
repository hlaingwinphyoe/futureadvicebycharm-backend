<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FrontController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route::post('/auth/{provider}/redirect', [AuthController::class, 'redirect']);
// Route::post('/auth/google/callback', [AuthController::class, 'callback']);

// blogs
Route::controller(PostController::class)->group(function () {
    Route::get('/posts-list', 'getPosts');
    Route::get('/posts-list/{slug}', 'getPost');
    Route::get('/posts-list/special/today', 'getTodaySpecial');
    Route::get('/posts-list/{id}/recent', 'getRecentPost');
});

Route::controller(FrontController::class)->group(function () {
    Route::get('/genders-list', 'getGenders');
    Route::get('/weekdays-list', 'getWeekdays');

    Route::get('/banks-list', 'getBanks');
    Route::get('/zodiacs-list', 'getZodiacs');
    Route::get('/packages-list', 'getPackages');
    Route::get('/packages-list/{id}', 'getPackage');
    Route::get('/packages-all', 'getPackagesAll');
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'getUser');
        Route::post('/refresh-token', 'refreshToken');
        Route::post('/logout', 'logout');
    });

    Route::controller(AppointmentController::class)->prefix('appointment')->group(function () {
        Route::post('/store', 'makeAppointment');
        Route::get('/{appointment_no}', 'getAppointment');
        Route::post('/{appointment_no}/payment/store', 'paymentStore');
    });
});
