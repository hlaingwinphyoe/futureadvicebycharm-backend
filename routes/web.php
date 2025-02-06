<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SystemInfoController;
use App\Http\Controllers\Admin\ZodiacController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    // Appointment
    Route::controller(AppointmentController::class)->prefix('/appointments')->name('appointments.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::patch('/{id}/{type}', 'update')->name('update');
    });

    // Reading Post
    Route::resource('/categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('/posts', PostController::class)->except(['show']);
    Route::controller(PostController::class)->prefix('posts')->name('posts.')->group(function () {
        Route::patch('/{post}/change-status', 'changeStatus')->name('change-status');
    });

    // Item
    Route::resource('/items', ItemController::class)->except(['create', 'show', 'edit']);
    Route::controller(ItemController::class)->prefix('items')->name('items.')->group(function () {
        Route::delete('/{item}/destroy-media', 'destroyMedia')->name('destroy-media');
        Route::patch('/{item}/change-status', 'changeStatus')->name('change-status');
    });

    // Package
    Route::resource('/packages', PackageController::class)->except(['create', 'show', 'edit']);
    Route::controller(PackageController::class)->prefix('packages')->name('packages.')->group(function () {
        Route::delete('/{package}/destroy-media', 'destroyMedia')->name('destroy-media');
        Route::patch('/{package}/change-status', 'changeStatus')->name('change-status');

        // remarks
        Route::get('/{package}/remarks', 'getRemarks')->name('get-remarks');
        Route::post('/{package}/add-remarks', 'addRemarks')->name('add-remarks');
    });

    // zodiac
    Route::resource('/zodiacs', ZodiacController::class)->except(['create', 'show', 'edit']);

    // staff & roles
    Route::resource('staffs', StaffController::class)->except(['create', 'show', 'edit']);
    Route::controller(StaffController::class)->prefix('/staffs')->name('staffs.')->group(function () {
        Route::patch('/{staff}/change-password', 'changePassword')->name('change-password');
        Route::delete('/{staff}/destroy-media', 'destroyMedia')->name('destroy-media');
    });
    Route::resource('banks', BankController::class)->except(['create', 'show', 'edit']);
    Route::controller(BankController::class)->prefix('banks')->name('banks.')->group(function () {
        Route::patch('/{bank}/change-status', 'changeStatus')->name('change-status');
    });
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);
    Route::controller(SystemInfoController::class)->prefix('/system-infos')->name('system-infos.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/update-info', 'updateInfo')->name('update');
        Route::patch('/{info}/add-phone', 'addPhone')->name('addPhone');
        Route::delete('/{info}/delete-phone/{phone}', 'deletePhone')->name('deletePhone');
        // Route::patch('/{info}/upload-logo', 'uploadLogo')->name('uploadLogo');
    });

    // customer
    Route::controller(CustomerController::class)->prefix('customers')->name('customers.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{customer}', 'show')->name('show');
        Route::patch('/{customer}/banned', 'toggleBan')->name('banned');
    });
});

Route::middleware('auth')->controller(ProfileController::class)->name('profile.')->group(function () {
    Route::get('/profile', 'edit')->name('edit');
    Route::patch('/profile', 'update')->name('update');
    Route::delete('/profile', 'destroy')->name('destroy');
});


Route::get('/page-not-found', function () {
    return Inertia::render('Error');
})->name('error');

Route::fallback(function () {
    return redirect()->route('error');
});

require __DIR__ . '/auth.php';
