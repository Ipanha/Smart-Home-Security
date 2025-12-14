<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AuthCredential;
use Illuminate\Support\Facades\Http;

class AdminWebController extends Controller
{
    // --- AUTHENTICATION ---

    public function showLogin() { 
        return view('admin.login'); 
    }

    public function login(Request $request) {
        $proxy = Request::create('/api/login', 'POST', $request->only(['email', 'password']));
        $proxy->headers->set('Accept', 'application/json');
        
        $response = Route::dispatch($proxy);
        $content = json_decode($response->getContent(), true);

        if ($response->status() === 200) {
            if (!str_contains($request->email, 'admin')) {
                return back()->withErrors(['msg' => 'Admins Only.']);
            }
            session(['admin_token' => $content['access_token']]);
            return redirect('/admin/users');
        }
        return back()->withErrors(['msg' => 'Invalid Credentials']);
    }

    public function logout() { 
        session()->forget('admin_token'); 
        return redirect('/admin/login'); 
    }

    public function dashboard() { 
        return redirect('/admin/users'); 
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

    // UPDATED: Fetch Homes AND map User Details to them
    public function homes() { 
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');
        
        $homes = [];
        $usersMap = []; // To store ID => [Name, Email]

        try {
            // 1. Fetch Users to build a lookup map
            $userResponse = Http::get('http://user-home-service:8000/api/all-users');
            if ($userResponse->successful()) {
                $uJson = $userResponse->json();
                $uList = $uJson['data']['data'] ?? $uJson['data'] ?? [];
                
                foreach ($uList as $u) {
                    $uid = $u['id'] ?? $u['_id'] ?? null;
                    if ($uid) {
                        $usersMap[$uid] = [
                            'name' => $u['name'] ?? 'Unknown',
                            'email' => $u['email'] ?? 'No Email'
                        ];
                    }
                }
            }

            // 2. Fetch Homes
            $response = Http::get('http://user-home-service:8000/api/homes');
            if ($response->successful()) {
                $rawHomes = $response->json()['data'] ?? [];
                
                // 3. Merge Owner Details into Homes
                $homes = array_map(function($home) use ($usersMap) {
                    $ownerId = $home['owner_id'] ?? null;
                    if ($ownerId && isset($usersMap[$ownerId])) {
                        $home['owner_name'] = $usersMap[$ownerId]['name'];
                        $home['owner_email'] = $usersMap[$ownerId]['email'];
                    } else {
                        $home['owner_name'] = 'Unknown Owner';
                        $home['owner_email'] = $ownerId; // Show ID if name not found
                    }
                    return $home;
                }, $rawHomes);
            }
        } catch (\Exception $e) {}
        
        return view('admin.dashboard', ['homes' => $homes, 'view_type' => 'homes']); 
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

    // --- ACTIONS ---

    public function createUser(Request $request) {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation']);
        
        $proxy = Request::create('/api/register', 'POST', [], [], [], 
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'], 
            json_encode($data)
        );
        
        $response = Route::dispatch($proxy);
        
        if ($response->status() === 201) return back()->with('success', 'User Created Successfully!');
        
        $content = json_decode($response->getContent(), true);
        $msg = $content['message'] ?? 'Error creating user';
        return back()->with('error', 'Failed: ' . $msg);
    }

    public function deleteUser($id) {
        if (!$id || $id === 'undefined') return back()->with('error', 'Error: User ID is missing.');

        $token = session('admin_token');
        $proxy = Request::create("/api/admin/users/{$id}", 'DELETE');
        $proxy->headers->set('Authorization', 'Bearer ' . $token);
        $response = Route::dispatch($proxy);

        if ($response->status() === 200) return back()->with('success', 'User Deleted Successfully');
        return back()->with('error', 'Failed to delete user.');
    }

    public function createHome(Request $request) {
        $token = session('admin_token');
        $response = Http::withToken($token)->post('http://user-home-service:8000/api/homes', [
            'name' => $request->name,
            'owner_id' => $request->owner_id
        ]);

        if ($response->successful()) return back()->with('success', 'Home Created!');
        return back()->with('error', 'Failed to create home.');
    }

    public function deleteHome($id) {
        $response = Http::delete("http://user-home-service:8000/api/homes/{$id}");
        if ($response->successful()) return back()->with('success', 'Home Deleted');
        return back()->with('error', 'Failed to delete home.');
    }

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