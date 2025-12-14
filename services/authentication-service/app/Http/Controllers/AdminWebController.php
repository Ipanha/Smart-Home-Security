<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AuthCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash; // Needed for update

class AdminWebController extends Controller
{
    // --- AUTHENTICATION ---

    public function showLogin() { 
        return view('admin.login'); 
    }

    public function login(Request $request) {
        // We keep Route::dispatch for login because it generates the JWT token for us
        $server = ['HTTP_ACCEPT' => 'application/json'];
        $proxy = Request::create('/api/login', 'POST', $request->only(['email', 'password']), [], [], $server);
        
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

    public function homes() { 
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');
        
        $homes = [];
        $usersMap = [];

        try {
            // Fetch Users for lookup
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

            // Fetch Homes
            $response = Http::get('http://user-home-service:8000/api/homes');
            if ($response->successful()) {
                $rawHomes = $response->json()['data'] ?? [];
                
                $homes = array_map(function($home) use ($usersMap) {
                    $ownerId = $home['owner_id'] ?? null;
                    if ($ownerId && isset($usersMap[$ownerId])) {
                        $home['owner_name'] = $usersMap[$ownerId]['name'];
                        $home['owner_email'] = $usersMap[$ownerId]['email'];
                    } else {
                        $home['owner_name'] = 'Unknown Owner';
                        $home['owner_email'] = $ownerId; 
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
        
        // We keep using proxy here because Register logic is complex (creates 2 records)
        // And register route is PUBLIC, so no Auth Headers needed.
        $server = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'];
        $proxy = Request::create('/api/register', 'POST', [], [], [], $server, json_encode($data));
        
        $response = Route::dispatch($proxy);
        
        if ($response->status() === 201) return back()->with('success', 'User Created Successfully!');
        
        $content = json_decode($response->getContent(), true);
        $msg = $content['message'] ?? 'Error creating user';
        return back()->with('error', 'Failed: ' . $msg);
    }

    // FIX: DIRECT LOGIC DELETE (Bypasses API Middleware issues)
    public function deleteUser($id) {
        if (!$id || $id === 'undefined') return back()->with('error', 'Error: User ID is missing.');

        // 1. Delete from Auth DB (Local)
        $deleted = AuthCredential::where('user_id', $id)->delete();
        if (!$deleted) {
             $deleted = AuthCredential::where('_id', $id)->delete();
        }

        // 2. Delete from User Profile DB (Remote Service)
        // We use Http facade to call the service directly
        try {
            Http::delete("http://user-home-service:8000/api/users/{$id}");
        } catch (\Exception $e) {
            // Even if remote fails, if local is deleted, we count it as success for admin
        }

        if ($deleted) {
            return back()->with('success', 'User Deleted Successfully');
        }

        return back()->with('error', 'User not found in Authentication Database.');
    }

    // FIX: DIRECT LOGIC UPDATE
    public function updateUser(Request $request, $id) {
        // 1. Find Auth Record
        $credential = AuthCredential::where('user_id', $id)->first();
        
        if (!$credential) {
            return back()->with('error', 'User not found.');
        }

        // 2. Update Local Auth Data
        $credential->email = $request->email;
        if ($request->filled('password')) {
            $credential->password = Hash::make($request->password);
        }
        $credential->save();

        // 3. Update Remote Profile Data
        try {
            Http::put("http://user-home-service:8000/api/users/{$id}", [
                'name' => $request->name,
                'email' => $request->email
            ]);
        } catch (\Exception $e) {
            return back()->with('warning', 'Auth updated, but Profile sync failed.');
        }

        return back()->with('success', 'User Updated Successfully');
    }

    public function createHome(Request $request) {
        $token = session('admin_token');
        // Remote call with token
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