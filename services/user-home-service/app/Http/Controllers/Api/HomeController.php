<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Home;
use App\Models\User;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\Log;
class HomeController extends Controller
{
    public function index()
    {
        $homes = Home::all();
        return response()->json(['data' => $homes], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'required',
        ]);

        $data = $validatedData;
        $data['members'] = [];

        $home = Home::create($data);

        return response()->json(['data' => $home], 201);
    }

    public function show(string $id)
    {
        $home = Home::findOrFail($id);
        return response()->json(['data' => $home]);
    }

    public function addMember(Request $request, $homeId)
    {
        $request->validate(['user_id' => 'required']);

        // 1. Sanitize User ID
        $rawUserId = $request->user_id;
        $cleanUserId = is_array($rawUserId) ? ($rawUserId['$oid'] ?? $rawUserId['id'] ?? '') : (string)$rawUserId;

        if (empty($cleanUserId)) {
            return response()->json(['message' => 'Invalid User ID'], 400);
        }

        // 2. Find Home (Handle string or ObjectId)
        $home = Home::where('_id', $homeId)->first();
        if (!$home) {
            try {
                $home = Home::where('_id', new ObjectId($homeId))->first();
            } catch (\Exception $e) {}
        }

        if (!$home) {
            return response()->json(['message' => 'Home not found'], 404);
        }

        // 3. Push to DB (Atomic)
        $home->push('members', $cleanUserId, true);

        return response()->json([
            'message' => 'Member added successfully', 
            'member_id' => $cleanUserId
        ], 200);
    }

    /**
     * HYBRID GET: Searches for IDs as both Strings and ObjectIds
     * to handle dirty data.
     */
    public function getUserHome($userId)
    {
        // 1. Find the home (Search by Owner OR Member)
        $home = Home::where('owner_id', $userId)
                    ->orWhere('members', $userId)
                    ->orWhere('members', (string)$userId)
                    ->first();

        if (!$home) {
            return response()->json(['data' => null]);
        }

        // 2. Get Owner
        $owner = User::find($home->owner_id);
        
        // 3. Get Members List Safe
        $rawMemberIds = $home->members;

        // Convert Collection/Null to Array
        if ($rawMemberIds instanceof \Illuminate\Support\Collection) {
            $rawMemberIds = $rawMemberIds->toArray();
        } elseif (!is_array($rawMemberIds)) {
            $rawMemberIds = [];
        }

        // 4. Create Hybrid Query List
        // We add BOTH the String version AND the ObjectId version of every ID.
        // This ensures a match regardless of data type.
        $queryIds = [];
        foreach ($rawMemberIds as $mid) {
            $strId = is_array($mid) ? ($mid['$oid'] ?? '') : (string)$mid;
            
            if (!empty($strId)) {
                // 1. Add as String
                $queryIds[] = $strId;
                
                // 2. Add as ObjectId (if valid hex)
                if (preg_match('/^[a-f\d]{24}$/i', $strId)) {
                    try {
                        $queryIds[] = new ObjectId($strId);
                    } catch (\Exception $e) {}
                }
            }
        }

        // 5. Fetch Users
        // The WhereIn will check both formats.
        $members = User::whereIn('_id', $queryIds)->get();

        return response()->json([
            'home' => $home,
            'owner' => $owner,
            'members' => $members
        ]);
    }
    
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate(['name' => 'required|string|max:255']);
        $home = Home::findOrFail($id);
        $home->update(['name' => $validatedData['name']]);
        return response()->json(['message' => 'Home updated', 'data' => $home], 200);
    }
    
    public function destroy($id) {
        $home = Home::findOrFail($id);
        $home->delete();
        return response()->json(['message' => 'Home Deleted']);
    }
}