<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new admin.
     */
    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Get admin role ID (integer only)
        $adminRoleId = Role::where('name', 'admin')->value('id');

        // Create the admin user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $adminRoleId,
        ]);

        // Create an API token for the new admin
        $token = $user->createToken('api-token')->plainTextToken;

        // Return admin info + token
        return response()->json([
            'message' => 'Admin created successfully!',
            'user' => $user,
            'api_token' => $token,
        ], 201);
    }

    /**
     * Register a new user.
     */
    public function register(AuthRegisterRequest $request)
    {

        $userRole = Role::where('name', 'user')->value('id');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully!',
            'user' => $user,
            'api_token' => $token,
        ], 201);
    }

    /**
     * Login a user.
     */
    public function login(AuthLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'user' => $user,
            'api_token' => $token,
        ], 200);
    }

    /**
     * Logout the current user.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Delete the current token
            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            // Or delete all tokens if you want to log out from all devices:
            // $user->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out successfully!'], 200);
    }

    /**
     * Check user role helper (optional)
     */
    protected function authorizeRole(array $roles)
    {
        $user = request()->user();
        if (!$user || !in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
