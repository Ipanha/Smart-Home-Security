<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    /**
     * Get all devices for a specific home.
     */
    public function index(string $homeId)
    {
        $devices = Device::where('home_id', $homeId)->get();
        return response()->json($devices);
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'home_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'type' => 'required|in:camera,door_lock,window_sensor,lamp,fire_alarm',
            'status' => 'required|string|max:50',
        ]);

        // Here you would normally verify that the home_id exists by calling the user-home-service.
        // For now, we will trust the input.

        $device = Device::create($validatedData);

        return response()->json($device, 201);
    }
}