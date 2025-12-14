<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Models\AuthCredential;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $messages = [
            'password.min' => 'Your password must be at least 8 characters long.',
        ];

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:auth_credentials',
            'password' => 'required|string|min:8|confirmed',
        ], $messages);

        $response = Http::post('http://user-home-service:8000/api/users', [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to create user profile', 'error' => $response->body()], 500);
        }

        // Handle both 'id' and '_id' from User Service
        $user_id = $response->json('data.id') ?? $response->json('data._id');
        
        if (!$user_id) {
            return response()->json(['message' => 'User ID missing from response', 'response' => $response->json()], 500);
        }

        $credential = AuthCredential::create([
            'user_id' => $user_id,
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'home_owner',
        ]);

        return response()->json(['message' => 'User registered successfully!', 'data' => $credential], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message'=>'Login Successfully',
            'access_token' => $token,
            // Include role in response so frontend knows who logged in
            'role' => auth('api')->user()->role 
        ]);
    }

    // NEW: Delete User Function
    public function deleteUser($id)
    {
        $user = auth('api')->user();

        // Security: Only Admins can delete users
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized. Admins only.'], 403);
        }

        // 1. Delete from Authentication DB
        $deleted = AuthCredential::where('user_id', $id)->delete();

        // 2. Delete from User Profile DB (Microservice Call)
        $response = Http::delete("http://user-home-service:8000/api/users/{$id}");

        if ($deleted) {
            return response()->json(['message' => 'User deleted successfully', 'profile_status' => $response->status()]);
        }

        return response()->json(['message' => 'User not found in Auth DB'], 404);
    }
}