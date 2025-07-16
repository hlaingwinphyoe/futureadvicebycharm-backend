<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FrontController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

// Route::post('/auth/{provider}/redirect', [AuthController::class, 'redirect']);
// Route::post('/auth/google/callback', [AuthController::class, 'callback']);

// blogs
Route::controller(PostController::class)->group(function () {
    Route::get('/posts-list', 'getPosts');
    Route::get('/posts-list/{slug}', 'getPost');
    Route::get('/today-special-post', 'getTodaySpecial');
    Route::get('/posts-list/{id}/recommended', 'getRecommendedPosts');
    Route::get('/popular-posts', 'getPopularPosts');

    Route::get('/all-posts', 'getAllPosts');

    Route::get('/posts/{id}/votes', 'upVoteDownVote');
    Route::post('/posts/{id}/votes/store', 'upVoteDownVoteStore')->middleware('auth:sanctum');

    Route::post('/posts/{post}/saved', 'savedPost')->middleware('auth:sanctum');
    Route::delete('/posts/{post}/unsaved', 'unSavedPost')->middleware('auth:sanctum');
});

Route::controller(FrontController::class)->group(function () {
    Route::get('/genders-list', 'getGenders');
    Route::get('/weekdays-list', 'getWeekdays');

    Route::get('/banks-list', 'getBanks');
    Route::get('/zodiacs-list', 'getZodiacs');
    Route::get('/packages-list', 'getPackages');
    Route::get('/packages-list/{id}', 'getPackage');
    Route::get('/packages-all', 'getPackagesAll');

    Route::get('/categories-list', 'getCategories');
    Route::post('/send-message', 'sendMessage');
    Route::get('/get-info', 'getInfo');
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'getUser');
        // Route::post('/refresh-token', 'refreshToken');
        Route::post('/logout', 'logout');
    });

    Route::controller(AppointmentController::class)->prefix('appointments')->group(function () {
        Route::post('/store', 'makeAppointment');
        Route::get('/{appointment_no}', 'getAppointment');
        Route::post('/{appointment_no}/payment/store', 'paymentStore');
        Route::get('/users/{id}/bookings', 'getBookings');
        Route::get('/bookings/full-days', 'getBookingsDays');
    });

    // account user
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::patch('/info/update', 'updateInfo');
        Route::patch('/password/update', 'updatePassword');
        Route::post('/avatar/upload', 'uploadAvatar');
        Route::get('/{id}/saved-posts', 'getSavedPosts');
    });
});
