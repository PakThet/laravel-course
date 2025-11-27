<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_permissions')->only(['index', 'show']);
        $this->middleware('permission:create_permissions')->only(['store']);    
        $this->middleware('permission:edit_permissions')->only(['update']);
        $this->middleware('permission:delete_permissions')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $permissions = Permission::when(
            $request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%")
        )->paginate($request->per_page ?? 10);
        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    public function show(Permission $permission)
    {
        return response()->json([
            'success' => true,
            'data' => $permission
        ]);
    }

    public function store(Request $request)
    {
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }

    public function update(Request $request, Permission $permission)
    {
        $permission->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully',
            'data' => $permission
        ]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
