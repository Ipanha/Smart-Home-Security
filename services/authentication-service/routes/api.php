<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/ping', fn () => response()->json(['message' => 'pong']));

Route::middleware('auth:api')->group(function () {

    // Admin user management
    Route::delete('/admin/users/{id}', [AuthController::class, 'deleteUser']);

    Route::get('/admin/users', function () {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $response = Http::get('http://user-home-service:8000/api/all-users');
        return response()->json($response->json(), $response->status());
    });

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

    Route::post('/devices', fn (Request $request) =>
        Http::post('http://device-service:8000/api/create-device', $request->all())->json()
    );
});
