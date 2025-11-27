<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:assign_roles')->only(['assignRole', 'removeRole']);
    }

    public function assignRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $user->syncRoles($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully',
            'data' => $user->roles
        ]);
    }

    public function revokeRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        foreach ($request->roles as $role) {
            $user->removeRole($role);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role removed successfully',
            'data' => $user->roles
        ]);
    }


    public function getUserRoles(User $user)
{
    // Load roles with their permissions
    $user->load('roles.permissions');

    // Prepare clean user data
    $userData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $user->avatar,
        'is_active' => $user->is_active,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ];

    // Get role names
    $roles = $user->getRoleNames();

    // Get all permissions (direct + via roles)
    $permissions = $user->getAllPermissions()->pluck('name');

    return response()->json([
        'success' => true,
        'data' => [
            'user' => $userData,
            'roles' => $roles,
            'permissions' => $permissions,
        ]
    ]);
}

}
