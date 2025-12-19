<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    
    // 1. CREATE
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'home_id' => 'required|string',
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

    // 2. READ (All Devices for Admin Dashboard)
    public function getAllDevices()
    {
        $devices = Device::all();
        return response()->json([
            'count' => $devices->count(),
            'data' => $devices
        ]);
    }

    // 2. READ (Specific Home Devices)
    public function index($homeId)
    {
        $devices = Device::where('home_id', (string)$homeId)->get();
        
        return response()->json([
            'home_id' => $homeId,
            'count' => $devices->count(),
            'data' => $devices
        ]);
    }

    // 3. UPDATE
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'home_id' => 'sometimes|string', // Allow updating Home ID
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string',
            'status' => 'sometimes|string'
        ]);

        $device = Device::find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->update($validatedData);

        return response()->json(['message' => 'Device updated', 'data' => $device]);
    }

    // 4. DELETE
    public function destroy($id)
    {
        $device = Device::find($id);
        
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $device->delete();

        return response()->json(['message' => 'Device deleted successfully']);
    }
}