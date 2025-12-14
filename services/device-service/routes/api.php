<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Standard Route
Route::post('/create-device', [DeviceController::class, 'store']);
Route::get('/homes/{homeId}/devices', [DeviceController::class, 'index']);

// 2. Gateway Fixes (Double API or Service Prefix)
// If the gateway sends '/api/create-device', this catches it.
Route::post('/api/create-device', [DeviceController::class, 'store']);
// If the gateway sends '/devices/create-device', this catches it.
Route::post('/devices/create-device', [DeviceController::class, 'store']);

Route::get('/ping', function () {
    return response()->json(['message' => 'Device Service Pong']);
});