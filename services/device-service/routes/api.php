<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

// Route to register a new device
Route::post('/devices', [DeviceController::class, 'store']);
// Route to get all devices for a specific home
Route::get('/homes/{homeId}/devices', [DeviceController::class, 'index']);