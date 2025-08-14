<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|unique:users',
                'password' => 'required|string|min:3',
                'gerai_id' => 'nullable|exists:gerais,id',
                'role' => 'nullable|string',
            ]);

            $user = User::create([
                'username' => $validated['username'],
                'password' => $validated['password'],
                'gerai_id' => $validated['gerai_id'] ?? null,
                'role' => $validated['role'] ?? 'ADMIN',
            ]);
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'token' => $token,
                'geraiId' => $user->gerai_id,
                'geraiName' => $user->gerai ? $user->gerai->name : null,
                'role' => $user->role,
                'message' => 'User registered successfully',
                'userId' => $user->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function guest(): JsonResponse
    {
        $user = new User();
        $user->username = "guest";
        $user->role = "GUEST";
          try {
                $token = JWTAuth::fromUser($user);
            } catch (JWTException $e) {
                Log::error('JWT generation error: ' . $e->getMessage());
                return response()->json(['message' => 'Could not create token', 'error' => $e->getMessage()], 500);
            }
             return response()->json([
                'token' => $token,
                'geraiId' => $user->gerai_id,
                'geraiName' => $user->gerai ? $user->gerai->name : null,
                'role' => $user->role,
            ]);
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('username', $credentials['username'])->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Invalid password'], 401);
            }

            try {
                $token = JWTAuth::fromUser($user);
            } catch (JWTException $e) {
                Log::error('JWT generation error: ' . $e->getMessage());
                return response()->json(['message' => 'Could not create token', 'error' => $e->getMessage()], 500);
            }
            return response()->json([
                'token' => $token,
                'geraiId' => $user->gerai_id,
                'geraiName' => $user->gerai ? $user->gerai->name : null,
                'role' => $user->role,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logged out']);
        } catch (JWTException $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }
}
