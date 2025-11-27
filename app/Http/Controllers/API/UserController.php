<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view_users')->only(['index', 'show']);
        $this->middleware('permission:create_users')->only(['store']);    
        $this->middleware('permission:edit_users')->only(['update', 'toggleStatus']);
        $this->middleware('permission:delete_users')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(UserRequest $request): JsonResponse
    {
        $userData = $request->validated();
        $userData['password'] = Hash::make($request->password);

        $user = User::create($userData);

        // If admin didn't choose roles → give default role
        if ($request->has('roles') && !empty($request->roles)) {
            $user->syncRoles($request->roles);
        } else {
            // Default role for new user
            $user->assignRole('user');
        }

        

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user->load('roles', 'permissions')
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load('roles', 'permissions');
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $userData = $request->validated();

        // Update password only if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        } else {
            unset($userData['password']);
        }

        $user->update($userData);

        // Sync roles if provided
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        $user->load('roles', 'permissions');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        // Prevent users from deleting themselves
        if (Auth::id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function toggleStatus(User $user): JsonResponse
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "User {$status} successfully",
            'data' => [
                'is_active' => $user->is_active
            ]
        ]);
    }

    public function getUserProfile(): JsonResponse
    {
        /**
         * @var mixed
         */
        $user = Auth::user();
        $user->load('roles', 'permissions');

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /**
         * @var mixed
         */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        /**
         * @var mixed
         */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    public function getRoles(): JsonResponse
    {
        $roles = Role::where('guard_name', 'api')->get();

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }
}