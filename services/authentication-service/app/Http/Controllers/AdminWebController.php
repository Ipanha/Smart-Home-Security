<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AuthCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class AdminWebController extends Controller
{
    // --- AUTHENTICATION ---

    public function showLogin() { 
        return view('admin.login'); 
    }

    public function login(Request $request) {
        $server = ['HTTP_ACCEPT' => 'application/json'];
        $proxy = Request::create('/api/login', 'POST', $request->only(['email', 'password']), [], [], $server);
        $response = Route::dispatch($proxy);
        $content = json_decode($response->getContent(), true);

        if ($response->status() === 200) {
            if (!str_contains($request->email, 'admin')) {
                return back()->withErrors(['msg' => 'Admins Only.']);
            }
            session(['admin_token' => $content['access_token']]);
            // CHANGED: Redirect to dashboard instead of users
            return redirect('/admin/dashboard');
        }
        return back()->withErrors(['msg' => 'Invalid Credentials']);
    }

    public function logout() { 
        session()->forget('admin_token'); 
        return redirect('/admin/login'); 
    }

    // --- FIX: ACTUAL DASHBOARD IMPLEMENTATION ---
    public function dashboard() { 
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');

        // 1. Fetch Users (for count)
        $users = AuthCredential::all(); 

        // 2. Fetch Homes (for count)
        $homes = [];
        try {
            $response = Http::get('http://user-home-service:8000/api/homes');
            if ($response->successful()) {
                $homes = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {}

        // 3. Fetch Devices (for count)
        $devices = [];
        try {
            $response = Http::get('http://device-service:8000/api/all-devices');
            if ($response->successful()) {
                $devices = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {}

        // Return view with 'dashboard' type
        return view('admin.dashboard', [
            'view_type' => 'dashboard',
            'users' => $users,
            'homes' => $homes,
            'devices' => $devices
        ]); 
    }

    // --- MAIN VIEWS ---

    public function users() {
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');

        $authUsers = AuthCredential::all(); 
        $profiles = [];

        try {
            $response = Http::get('http://user-home-service:8000/api/all-users');
            if ($response->successful()) {
                $json = $response->json();
                $list = $json['data']['data'] ?? $json['data'] ?? [];
                foreach ($list as $p) {
                    $id = $p['id'] ?? $p['_id'] ?? null;
                    if ($id) $profiles[$id] = $p['name'] ?? 'No Name';
                }
            }
        } catch (\Exception $e) {}

        $users = $authUsers->map(function ($user) use ($profiles) {
            if ($user->user_id === 'ADMIN_MASTER_ID') {
                $user->name = 'System Admin';
            } else {
                $user->name = $profiles[$user->user_id] ?? 'Unknown ID';
            }
            return $user;
        });

        return view('admin.dashboard', ['users' => $users, 'view_type' => 'users']);
    }

    public function homes()
{
    $token = session('admin_token');
    if (!$token) return redirect('/admin/login');

    $homes = [];
    $users = []; // ← ADD THIS

    try {
        // Fetch users (for dropdown)
        $userResponse = Http::get('http://user-home-service:8000/api/all-users');
        if ($userResponse->successful()) {
            $json = $userResponse->json();
            $users = $json['data']['data'] ?? $json['data'] ?? [];
        }

        // Fetch homes
        $response = Http::get('http://user-home-service:8000/api/homes');
        if ($response->successful()) {
            $homes = $response->json()['data'] ?? [];
        }
    } catch (\Exception $e) {}

    return view('admin.dashboard', [
        'view_type' => 'homes',
        'homes' => $homes,
        'users' => $users // ✅ REQUIRED
    ]);
}


    public function devices() { 
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');
        
        $devices = [];
        try {
            $response = Http::get('http://device-service:8000/api/all-devices');
            if ($response->successful()) {
                $devices = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {}
        
        return view('admin.dashboard', ['devices' => $devices, 'view_type' => 'devices']); 
    }

    // --- USER ACTIONS ---

    // CREATE USER
    public function createUser(Request $request) {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation']);
        $server = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'];
        $proxy = Request::create('/api/register', 'POST', [], [], [], $server, json_encode($data));
        $response = Route::dispatch($proxy);

        if ($response->status() === 201) return back()->with('success', 'User Created Successfully!');
        
        $content = json_decode($response->getContent(), true);
        $msg = $content['message'] ?? 'Error creating user';
        return back()->with('error', 'Failed: ' . $msg);
    }

    // UPDATE USER
    public function updateUser(Request $request, $id)
    {
        $credential = AuthCredential::where('user_id', $id)->first();
        if (!$credential) return back()->with('error', 'User not found.');

        $credential->email = $request->email;
        if ($request->filled('password')) $credential->password = Hash::make($request->password);
        $credential->save();

        try {
            $response = Http::put("http://user-home-service:8000/api/users/{$id}", [
                'name' => $request->name,
                'email' => $request->email
            ]);
            if ($response->failed()) {
                return back()->with('warning', 'Auth updated but profile update failed.');
            }
        } catch (\Exception $e) {
            return back()->with('warning', 'Auth updated but profile update failed.');
        }

        return back()->with('success', 'User Updated Successfully');
    }

    // DELETE USER
    public function deleteUser($id)
    {
        if (!$id || $id === 'undefined') return back()->with('error', 'Error: User ID is missing.');

        $deleted = AuthCredential::where('user_id', $id)->delete();
        if (!$deleted) $deleted = AuthCredential::where('_id', $id)->delete();

        try {
            $response = Http::delete("http://user-home-service:8000/api/users/{$id}");
            if ($response->failed()) {
                return back()->with('warning', 'Deleted from Auth but failed to delete profile.');
            }
        } catch (\Exception $e) {
            return back()->with('warning', 'Deleted from Auth but failed to delete profile.');
        }

        if ($deleted) return back()->with('success', 'User Deleted Successfully');
        return back()->with('error', 'User not found in Authentication Database.');
    }

    // --- HOME ACTIONS ----
    
    public function createHome(Request $request) {
        $token = session('admin_token');
        
        // 1. Send data to User-Home Service
        $response = Http::withToken($token)->post('http://user-home-service:8000/api/homes', [
            'name' => $request->name,
            'owner_id' => $request->owner_id // This comes from the hidden input in home.blade.php
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Home Created Successfully!');
        }
        
        return back()->with('error', 'Failed to create home. API Error.');
    }

    // NEW: Update Home Function
    public function updateHome(Request $request, $id) {
        $token = session('admin_token');

        // 1. Send PUT request to User-Home Service
        $response = Http::withToken($token)->put("http://user-home-service:8000/api/homes/{$id}", [
            'name' => $request->name,
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Home Updated Successfully!');
        }

        return back()->with('error', 'Failed to update home.');
    }

    public function deleteHome($id) {
        $response = Http::delete("http://user-home-service:8000/api/homes/{$id}");
        if ($response->successful()) return back()->with('success', 'Home Deleted');
        return back()->with('error', 'Failed to delete home.');
    }

    // --- DEVICE ACTIONS ---
    public function createDevice(Request $request) {
        $response = Http::post('http://device-service:8000/api/create-device', [
            'home_id' => $request->home_id,
            'name' => $request->name,
            'type' => $request->type,
            'status' => 'active'
        ]);

        if ($response->successful()) return back()->with('success', 'Device Created!');
        return back()->with('error', 'Failed to create device.');
    }

    public function deleteDevice($id) {
        $response = Http::delete("http://device-service:8000/api/devices/{$id}");
        if ($response->successful()) return back()->with('success', 'Device Deleted');
        return back()->with('error', 'Failed to delete device.');
    }

    public function viewUserHomes($userId) {
        $response = Http::get("http://user-home-service:8000/api/homes?owner_id={$userId}");
        $homes = $response->json()['data'] ?? [];
        return view('admin.homes', compact('homes', 'userId'));
    }

    public function viewHomeDevices($homeId) {
        $response = Http::get("http://device-service:8000/api/homes/{$homeId}/devices");
        $devices = $response->json()['data'] ?? [];
        return view('admin.devices', compact('devices', 'homeId'));
    }
}