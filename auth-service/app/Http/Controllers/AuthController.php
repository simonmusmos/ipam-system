<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Traits\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use AuditLogger;
    
    public function register(Request $request) {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255', // Name is required, must be a string, and max 255 characters
            'email' => 'required|email|unique:users', // Email is required, must be valid and unique in the users table
            'password' => 'required|min:6|confirmed', // Password is required and must be at least 6 characters
        ]);
    
        $role = Role::where('name', 'user')->first();

        // Create a new user with the validated data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before storing
            'role_id' => $role->id,
        ]);
    
        // Generate a JWT token for the newly created user
        $token = JWTAuth::claims(['role' => $user->role->name])->fromUser($user);
    
        // Return the user and token as a JSON response
        return response()->json(compact('user','token'));
    }

    public function login(Request $request) {
        // Retrieve only the email and password from the request
        $credentials = $request->only('email', 'password');
    
        // Attempt to authenticate the user using the credentials
        // If authentication fails, return an "Unauthorized" response
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the authenticated user
        $user = auth()->user();

        $token = JWTAuth::claims([
            'role' => $user->role->name,
        ])->fromUser($user);
    
        // Log login
        $this->logAudit($request, 'login', 'auth', $user->id);

        // If authentication is successful, return the JWT token in the response
        return response()->json(compact('token'));
    }

    public function me() {
        // Return the currently authenticated user's data as JSON
        return response()->json(auth()->user());
    }

    public function logout(Request $request) {
        // Log logout
        $this->logAudit($request, 'logout', 'auth', auth()->user()->id);

        // Invalidate the current user's JWT token (logout)
        auth()->logout();
    
        // Return a success message as JSON
        return response()->json(['message' => 'Logged out']);
    }

    public function refresh() {
        // Refresh the current JWT token and return a new one in the response
        try {
            $user = auth()->user();

            $newToken = JWTAuth::invalidate(JWTAuth::getToken());
            $newToken = JWTAuth::fromUser($user);

            return response()->json([
                'new_token' => $newToken
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        }
    }

    public function validateToken() {
        try {
            $user = auth()->user();

            return response()->json([
                'valid' => true,
                'user_id' => $user->id,
                'role' => $user->role->name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        }
    }
}
