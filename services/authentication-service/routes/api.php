<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\AuthController;

// --- 1. PUBLIC ROUTES (Keep these) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ping', fn () => response()->json(['message' => 'pong']));

// --- 2. PROTECTED ROUTES ---
Route::middleware('auth:api')->group(function () {

    // ==========================================
    //  SECTION A: YOUR EXISTING ROUTES
    //  (Do not delete these)
    // ==========================================

    // Admin User Management
    Route::delete('/admin/users/{id}', [AuthController::class, 'deleteUser']);

    Route::get('/admin/users', function () {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $response = Http::get('http://user-home-service:8000/api/all-users');
        return response()->json($response->json(), $response->status());
    });

    // Create Home (Used by Admin/Postman)
    Route::post('/homes', function (Request $request) {
        $user = auth()->user();
        $ownerId = ($user->role === 'admin' && $request->has('owner_id'))
            ? $request->owner_id
            : $user->user_id;

        return Http::post('http://user-home-service:8000/api/homes', [
            'name' => $request->name,
            'owner_id' => $ownerId,
            'members' => []
        ])->json();
    });

    // Create Device (Used by Admin/Postman)
    Route::post('/devices', fn (Request $request) =>
        Http::post('http://device-service:8000/api/create-device', $request->all())->json()
    );

    // ==========================================
    //  SECTION B: NEW MOBILE APP ROUTES
    //  (Added to support Flutter)
    // ==========================================

    // 1. GET HOME DETAILS (For Mobile Dashboard)
    // Proxies to User-Home-Service to get owner info + member list
    Route::get('/users/{id}/home-details', function ($id) {
        $response = Http::get("http://user-home-service:8000/api/users/{$id}/home-details");
        return response()->json($response->json(), $response->status());
    });

    // 2. GET DEVICES (For Mobile Dashboard)
    // Proxies to Device-Service to list devices in a home
    Route::get('/homes/{homeId}/devices', function ($homeId) {
        $response = Http::get("http://device-service:8000/api/homes/{$homeId}/devices");
        return response()->json($response->json(), $response->status());
    });

    // 3. TOGGLE DEVICE STATUS (For Mobile Switch)
    Route::put('/devices/{id}', function (Request $request, $id) {
        $response = Http::put("http://device-service:8000/api/devices/{$id}", $request->all());
        return response()->json($response->json(), $response->status());
    });

    // 4. ADD MEMBER (For Mobile "Add" Button)
    Route::post('/homes/{homeId}/members', function (Request $request, $homeId) {
        $response = Http::post("http://user-home-service:8000/api/homes/{$homeId}/members", $request->all());
        return response()->json($response->json(), $response->status());
    });

    // 5. LOGOUT
    Route::post('/logout', function (Request $request) {
       $request->user()->token()->revoke();
       return response()->json(['message' => 'Logged out']);
    });
});