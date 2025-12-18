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

        $users = AuthCredential::all(); 
        $homes = [];
        $devices = [];

        try {
            // Fetch raw data
            $hRes = Http::get('http://user-home-service:8000/api/homes');
            if ($hRes->successful()) $homes = $hRes->json()['data'] ?? [];
            
            $dRes = Http::get('http://device-service:8000/api/all-devices');
            if ($dRes->successful()) $devices = $dRes->json()['data'] ?? [];
        } catch (\Exception $e) {}

        return view('admin.dashboard', [
            'view_type' => 'dashboard',
            'users' => $users,
            'homes' => $homes,
            'devices' => $devices
        ]); 
    }

    // --- MAIN VIEWS ---

    // --- USERS LIST (Updated with Home & Date Logic) ---
    public function users() {
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');

        $authUsers = AuthCredential::all(); 
        $userProfiles = [];
        $userHomes = [];

        try {
            // 1. Fetch Users
            $uRes = Http::get('http://user-home-service:8000/api/all-users');
            if ($uRes->successful()) {
                $rawUsers = $uRes->json()['data']['data'] ?? $uRes->json()['data'] ?? [];
                
                // Index by ID for easy lookup
                foreach ($rawUsers as $u) {
                    $id = $u['id'] ?? $u['_id'] ?? null;
                    if ($id) $userProfiles[$id] = $u;
                }
            }

            // 2. Fetch Homes (To map User -> Home)
            $hRes = Http::get('http://user-home-service:8000/api/homes');
            if ($hRes->successful()) {
                $homes = $hRes->json()['data'] ?? [];
                foreach ($homes as $home) {
                    $homeName = $home['name'];
                    // Map Owner
                    $ownerId = $home['owner_id'] ?? '';
                    if(is_array($ownerId)) $ownerId = $ownerId['$oid'];
                    $userHomes[$ownerId] = $homeName;

                    // Map Members
                    $members = $home['members'] ?? [];
                    foreach($members as $mid) {
                        if(is_array($mid)) $mid = $mid['$oid'];
                        $userHomes[$mid] = $homeName;
                    }
                }
            }

        } catch (\Exception $e) {}

        // Merge Data
        $users = $authUsers->map(function ($auth) use ($userProfiles, $userHomes) {
            $uid = $auth->user_id;
            $profile = $userProfiles[$uid] ?? [];
            
            $auth->name = $profile['name'] ?? 'Unknown';
            $auth->profile_pic = $profile['profile_pic'] ?? null; // Pic URL
            $auth->home_name = $userHomes[$uid] ?? 'No Home Assigned';
            
            // Format Date (MongoDB dates can be tricky string/array)
            $rawDate = $profile['created_at'] ?? now();
            $auth->joined_date = \Carbon\Carbon::parse($rawDate)->format('M d, Y');
            
            return $auth;
        });

        return view('admin.dashboard', ['users' => $users, 'view_type' => 'users']);
    }

    // --- UPDATE USER (Handle Image Upload) ---
    public function updateUser(Request $request, $id)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email
        ];

        // 1. Handle File Upload
        if ($request->hasFile('profile_pic')) {
            // Store file in public/profiles
            $path = $request->file('profile_pic')->store('profiles', 'public');
            // Create full URL (accessible by browser)
            $url = asset('storage/' . $path); 
            $data['profile_pic'] = $url;
        }

        // 2. Update Auth DB
        $credential = AuthCredential::where('user_id', $id)->first();
        if ($credential) {
            $credential->email = $request->email;
            if ($request->filled('password')) $credential->password = Hash::make($request->password);
            $credential->save();
        }

        // 3. Update User-Home Service
        try {
            Http::put("http://user-home-service:8000/api/users/{$id}", $data);
        } catch (\Exception $e) {
            return back()->with('warning', 'Auth updated but profile sync failed.');
        }

        return back()->with('success', 'User Updated Successfully');
    }

    // --- USER DETAIL PAGE ---
    public function userDetails($id)
    {
        try {
            // 1. Get User Info
            $uRes = Http::get("http://user-home-service:8000/api/users/{$id}");
            $user = $uRes->json()['data'] ?? null;

            if(!$user) return redirect('/admin/users')->with('error', 'User not found');

            // 2. Get Home & Family Info
            $hRes = Http::get("http://user-home-service:8000/api/users/{$id}/home-details");
            $homeData = $hRes->json(); // Contains 'home', 'owner', 'members'

            return view('admin.user_detail', compact('user', 'homeData'));

        } catch (\Exception $e) {
            return back()->with('error', 'Service unreachable');
        }
    }

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

    public function homes() { 
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');
        
        $homes = [];
        $allUsers = []; 

        try {
            // 1. Fetch Users
            $userResponse = Http::get('http://user-home-service:8000/api/all-users');
            if ($userResponse->successful()) {
                $uJson = $userResponse->json();
                $allUsers = $uJson['data']['data'] ?? $uJson['data'] ?? [];
            }

            // 2. Fetch Homes
            $response = Http::get('http://user-home-service:8000/api/homes');
            if ($response->successful()) {
                $rawHomes = $response->json()['data'] ?? [];
                
                // 3. Build User Map (ID => Name)
                $usersMap = [];
                foreach ($allUsers as $u) {
                    // Handle different ID formats (array vs string)
                    $uid = $u['id'] ?? $u['_id'] ?? null;
                    
                    // If Mongo returns ID as array ['$oid' => '...'], extract it
                    if (is_array($uid) && isset($uid['$oid'])) {
                        $uid = $uid['$oid'];
                    }

                    if ($uid) {
                        $usersMap[(string)$uid] = $u['name'] ?? 'Unknown';
                    }
                }

                // 4. Map Owners to Homes
                $homes = array_map(function($home) use ($usersMap) {
                    $ownerId = $home['owner_id'] ?? null;
                    
                    // Handle Mongo ID format for owner_id too
                    if (is_array($ownerId) && isset($ownerId['$oid'])) {
                        $ownerId = $ownerId['$oid'];
                    }

                    // Look up name
                    $home['owner_name'] = isset($usersMap[(string)$ownerId]) 
                        ? $usersMap[(string)$ownerId] 
                        : 'Unknown Owner';
                        
                    return $home;
                }, $rawHomes);
            }
        } catch (\Exception $e) {
            // Keep empty on error
        }
        
        return view('admin.dashboard', [
            'homes' => $homes, 
            'users' => $allUsers, 
            'view_type' => 'homes'
        ]); 
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

    // --- HOME ACTIONS ----
    
    // --- HOME ACTIONS (FIXED) ---

    public function createHome(Request $request) {
        $token = session('admin_token');
        $response = Http::withToken($token)->post('http://user-home-service:8000/api/homes', [
            'name' => $request->name,
            'owner_id' => $request->owner_id
        ]);
        if ($response->successful()) return back()->with('success', 'Home Created!');
        return back()->with('error', 'Failed: ' . $response->body());
    }

    public function updateHome(Request $request, $id) {
        $token = session('admin_token');
        $response = Http::withToken($token)->put("http://user-home-service:8000/api/homes/{$id}", [
            'name' => $request->name,
        ]);
        
        if ($response->successful()) return back()->with('success', 'Home Updated!');
        return back()->with('error', 'Failed to update home: ' . $response->status());
    }

    public function deleteHome($id) {
        $response = Http::delete("http://user-home-service:8000/api/homes/{$id}");
        if ($response->successful()) return back()->with('success', 'Home Deleted');
        return back()->with('error', 'Failed to delete home.');
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

    // --- DASHBOARD VIEW ---
    public function devices() { 
        $token = session('admin_token');
        if (!$token) return redirect('/admin/login');
        
        $devices = [];
        $homes = [];

        try {
            // 1. Fetch All Homes first to create a lookup map
            $homeResponse = Http::get('http://user-home-service:8000/api/homes');
            $homeMap = []; // Key: ID, Value: Name
            
            if ($homeResponse->successful()) {
                $homes = $homeResponse->json()['data'] ?? [];
                foreach($homes as $home) {
                    // Handle MongoDB ID format
                    $hid = $home['id'] ?? $home['_id'] ?? ($home['id']['$oid'] ?? '');
                    if(is_array($hid) && isset($hid['$oid'])) $hid = $hid['$oid'];
                    
                    if($hid) $homeMap[(string)$hid] = $home['name'];
                }
            }

            // 2. Fetch Devices
            $devResponse = Http::get('http://device-service:8000/api/all-devices');
            if ($devResponse->successful()) {
                $rawDevices = $devResponse->json()['data'] ?? [];
                
                // 3. Map Home Name to Device
                $devices = array_map(function($device) use ($homeMap) {
                    $hid = $device['home_id'] ?? '';
                    $device['home_name'] = $homeMap[$hid] ?? 'Unknown Home';
                    return $device;
                }, $rawDevices);
            }

        } catch (\Exception $e) {}
        
        return view('admin.dashboard', [
            'devices' => $devices, 
            'homes' => $homes, 
            'view_type' => 'devices'
        ]); 
    }

    // --- CREATE DEVICE ---
    public function createDevice(Request $request) {
        $response = Http::post('http://device-service:8000/api/create-device', [
            'home_id' => $request->home_id,
            'name' => $request->name,
            'type' => $request->type,
            'status' => $request->status ?? 'active'
        ]);

        if ($response->successful()) return back()->with('success', 'Device Created!');
        return back()->with('error', 'Failed: ' . $response->body());
    }

    // --- UPDATE DEVICE ---
    public function updateDevice(Request $request, $id) {
        $response = Http::put("http://device-service:8000/api/devices/{$id}", [
            'home_id' => $request->home_id, // Pass the new Home ID
            'name' => $request->name,
            'type' => $request->type,
            'status' => $request->status
        ]);

        if ($response->successful()) return back()->with('success', 'Device Updated!');
        return back()->with('error', 'Failed: ' . $response->body());
    }

    // --- DELETE DEVICE ---
    public function deleteDevice($id) {
        $response = Http::delete("http://device-service:8000/api/devices/{$id}");
        if ($response->successful()) return back()->with('success', 'Device Deleted');
        return back()->with('error', 'Failed to delete device.');
    }
}