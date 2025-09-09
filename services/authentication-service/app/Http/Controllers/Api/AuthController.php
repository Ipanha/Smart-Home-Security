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
        // Define custom validation messages
        $messages = [
            'password.min' => 'Your password must be at least 8 characters long.',
        ];

        // 1. Validate incoming data using the custom messages
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:auth_credentials',
            'password' => 'required|string|min:8|confirmed',
        ], $messages); // <-- Pass the messages array here

        // 2. Ask the User & Home Service to create the user profile
        $response = Http::post('http://user-home-service:8000/api/users', [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to create user profile', 'error' => $response->body()], 500);
        }

        // 3. Get the new user ID from the response
        $user_id = $response->json('id');

        // 4. Create the secure credential in this service's database
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
            'access_token' => $token]);
    }
}