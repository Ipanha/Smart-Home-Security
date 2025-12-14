<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Standard Route (Listens for 'api/register')
// This works if the Gateway strips the prefix correctly.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 2. Double-API Route (Listens for 'api/api/register')
// This fixes the 404 you are seeing in the Debug output!
// Since this file is already prefixed with 'api', adding '/api/register' here creates 'api/api/register'.
Route::post('/api/register', [AuthController::class, 'register']);
Route::post('/api/login', [AuthController::class, 'login']);


// 3. Simple Ping Test
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// 4. THE DEBUGGER (Catches any route that didn't match above)
Route::any('{any}', function (Request $request) {
    return response()->json([
        'debug_status' => 'Route Missed',
        'message' => 'The request reached Laravel, but did not match any route.',
        'what_laravel_saw' => [
            'path' => $request->path(),
            'method' => $request->method(),
        ],
        'available_routes' => [
            'api/register',
            'api/api/register'
        ]
    ], 404);
})->where('any', '.*');