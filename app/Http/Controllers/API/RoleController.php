<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected array $protectedRoles = ['super admin']; // cannot delete or modify these roles
    protected array $protectedPermissions = ['delete_roles', 'edit_roles', 'create_roles']; // cannot revoke from protected roles

    public function __construct()
    {
        $this->middleware('permission:view_roles')->only(['index', 'show']);
        $this->middleware('permission:create_roles')->only(['store']);    
        $this->middleware('permission:edit_roles')->only(['update']);
        $this->middleware('permission:delete_roles')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $roles = Role::with('permissions')->when(
            $request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%")
        )->paginate($request->per_page ?? 10);
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('name', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }
        $role->load('permissions');
        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    public function update(Request $request, Role $role)
    {
        // Prevent updating protected roles
        if (in_array(strtolower($role->name), $this->protectedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'This role cannot be updated.'
            ], 403);
        }

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('name', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }
        $role->load('permissions');
        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    public function destroy(Role $role)
    {
        // Prevent deleting protected roles
        if (in_array(strtolower($role->name), $this->protectedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'This role cannot be deleted.'
            ], 403);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->hasRole($role->name)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a role assigned to the current user.'
            ], 403);
        }
        $role->delete();
        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $permissions = Permission::whereIn('name', $request->permissions)->get();
        $role->givePermissionTo($permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned to role successfully',
            'data' => $role
        ]);
    }

    public function revokePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Prevent revoking critical permissions from protected roles
        $revokablePermissions = array_diff($request->permissions, $this->protectedPermissions);
        if (empty($revokablePermissions)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot revoke protected permissions from this role.'
            ], 403);
        }
        
        $role->revokePermissionTo($request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions revoked from role successfully',
            'data' => $role
        ]);
    }
    
}
