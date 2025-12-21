<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Home; // Import Home model
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * List users (pagination)
     */
    public function index()
    {
        // Eager load homes to avoid N+1 queries if possible, or just paginate
        $users = User::paginate(15);
        return response()->json(['data' => $users]);
    }

    /**
     * Show single user
     */
    public function show($id)
    {
        try {
            // FIX: Robust ID search (String OR ObjectId)
            $user = User::where('_id', $id)
                ->orWhere('_id', new ObjectId($id))
                ->firstOrFail();

            return response()->json(['data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Create user
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6', // Added password validation
                'home_id' => 'nullable|string'         // Allow assigning home on create
            ]);

            $user = User::create([
                'name'  => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'role' => 'member',
                'profile_pic' => null, // Default
            ]);

            // Handle Initial Home Assignment
            if (!empty($request->home_id)) {
                $this->assignToHome($user->id, $request->home_id);
            }

            return response()->json([
                'message' => 'Create User Successfully',
                'data' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Invalid Data', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        try {
            // 1. Find User (Robustly)
            $user = User::where('_id', $id)->orWhere('_id', new ObjectId($id))->firstOrFail();

            // 2. Validate
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id, // Use actual object ID for exclusion
                'profile_pic' => 'nullable|image|max:2048', // FIX: Validate as Image, not String
                'home_id' => 'nullable|string', // FIX: Accept home_id from form
                'password' => 'nullable|string|min:6'
            ]);

            // 3. Update Basic Fields
            $user->name = $data['name'];
            $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = bcrypt($data['password']);
            }

            // 4. FIX: Handle Profile Picture Upload
            if ($request->hasFile('profile_pic')) {
                // Store file in 'public/profile_pics'
                $path = $request->file('profile_pic')->store('profile_pics', 'public');
                // Save URL/Path to DB
                $user->profile_pic = '/storage/' . $path; 
            }

            $user->save();

            // 5. FIX: Handle Home Re-assignment
            // If home_id is provided in the request, update the relationship
            if ($request->has('home_id')) {
                $newHomeId = $request->home_id;
                $this->updateHomeAssignment($user->id, $newHomeId);
            }

            // Return Redirect back (because your Dashboard uses standard Form Submit)
            // Or return JSON if you are using AJAX. 
            // Based on your Blade, a redirect is safer:
            return redirect()->back()->with('success', 'User updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Remove user from any homes before deleting
        $this->updateHomeAssignment($user->id, null);
        
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    /**
     * Helper to handle moving a user from one home to another
     */
    private function updateHomeAssignment($userId, $newHomeId)
    {
        // 1. Remove user from ALL current homes (Since UI implies single home)
        $currentHomes = Home::where('members', $userId)
                            ->orWhere('members', (string)$userId)
                            ->get();

        foreach ($currentHomes as $h) {
            // Only pull if it's NOT the new home
            if ($h->id != $newHomeId) {
                $h->pull('members', (string)$userId); // Remove ID string
                $h->pull('members', new ObjectId($userId)); // Remove ID Object
            }
        }

        // 2. Add to New Home (Atomic Push)
        if (!empty($newHomeId)) {
            $newHome = Home::find($newHomeId);
            if ($newHome) {
                // Convert User ID to string for consistent storage in Array
                $strUserId = (string)$userId; 
                $newHome->push('members', $strUserId, true); // true = unique
            }
        }
    }
}