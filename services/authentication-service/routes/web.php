<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminWebController;

Route::get('/', function () {
    return redirect('/admin/login');
});

// --- AUTH ---
Route::get('/admin/login', [AdminWebController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AdminWebController::class, 'login']);
Route::post('/admin/logout', [AdminWebController::class, 'logout']);

// --- DASHBOARD PAGES ---
Route::middleware(['web'])->group(function () {
    Route::get('/admin/dashboard', [AdminWebController::class, 'dashboard']);
    Route::get('/admin/users', [AdminWebController::class, 'users']);
    Route::get('/admin/homes', [AdminWebController::class, 'homes']);
    Route::get('/admin/devices', [AdminWebController::class, 'devices']);

    // --- CRUD ACTIONS ---
    // Users
    Route::post('/admin/create-user', [AdminWebController::class, 'createUser']);
    Route::post('/admin/delete-user/{id}', [AdminWebController::class, 'deleteUser']);

    // Homes
    Route::post('/admin/create-home', [AdminWebController::class, 'createHome']);
    Route::post('/admin/delete-home/{id}', [AdminWebController::class, 'deleteHome']);

    // Devices
    Route::post('/admin/create-device', [AdminWebController::class, 'createDevice']);
    Route::post('/admin/delete-device/{id}', [AdminWebController::class, 'deleteDevice']);
});