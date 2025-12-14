<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

Route::post('/create-device', [DeviceController::class, 'store']);
// Fix for Gateway
Route::post('/api/create-device', [DeviceController::class, 'store']); 

Route::get('/homes/{homeId}/devices', [DeviceController::class, 'index']);
Route::get('/api/homes/{homeId}/devices', [DeviceController::class, 'index']); // Gateway fix

// DELETE DEVICE
Route::delete('/devices/{id}', function ($id) {
    \App\Models\Device::destroy($id);
    return response()->json(['message' => 'Device Deleted']);
});
Route::delete('/api/devices/{id}', function ($id) { // Gateway fix
    \App\Models\Device::destroy($id);
    return response()->json(['message' => 'Device Deleted']);
});