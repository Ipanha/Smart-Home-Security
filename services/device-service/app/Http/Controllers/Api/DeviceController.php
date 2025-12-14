<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'home_id' => 'required|string', // MongoDB IDs are strings
            'name' => 'required|string|max:255',
            'type' => 'required|string',   
            'status' => 'required|string', 
        ]);

        $device = Device::create($validatedData);

        return response()->json([
            'message' => 'Device created successfully',
            'data' => $device
        ], 201);
    }

    /**
     * Display a listing of devices for a specific home.
     */
    public function index($homeId)
    {
        $devices = Device::where('home_id', $homeId)->get();

        return response()->json([
            'home_id' => $homeId,
            'count' => $devices->count(),
            'data' => $devices
        ]);
    }
}