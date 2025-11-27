<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view_users')->only(['getAllUsers']);
    // }


    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:6|confirmed'
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //         'is_active' => true,
    //     ]);
    //     $user->assignRole('user');

    //     $token = $user->createToken('api_token')->plainTextToken;

    //     return response()->json([
    //         'success' => true, 
    //         'token' => $token, 
    //         'user' => $user->load('roles', 'permissions')
    //     ]);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUser($user)
        ]);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);

    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $this->formatUser($request->user())
        ]);

    }



public function getAllUsers()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Get all users if has permission, otherwise just self
        $users = $user->hasPermissionTo('view_users', 'api') 
            ? User::with('roles.permissions')->get()
            : User::with('roles.permissions')->where('id', $user->id)->get();

        // Format each user to include effective permissions
        $users = $users->map(fn($u) => $this->formatUser($u));

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }


    protected function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'roles' => $user->getRoleNames(), 
            'permissions' => $user->getAllPermissions()->pluck('name') 
        ];
    }





}
