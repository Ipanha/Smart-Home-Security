<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

Route::get('/homes/{homeId}/devices', [DeviceController::class, 'index']);
// Create
Route::post('/create-device', [DeviceController::class, 'store']);

// Read
Route::get('/all-devices', [DeviceController::class, 'getAllDevices']); // For Admin Dashboard
Route::get('/homes/{homeId}/devices', [DeviceController::class, 'index']);

// Update
Route::put('/devices/{id}', [DeviceController::class, 'update']);

// Delete
Route::delete('/devices/{id}', [DeviceController::class, 'destroy']);
