<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$role)

    {
        $user = Auth::guard('api')->user();
        Log::info('User role: ', ['role' => $user->role]);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized: No user'], 401);
        }

        if (!in_array($user->role, $role)) {
            Log::info('Role tidak valid, user role: ' . $user->role . ', allowed roles: ' . implode(', ', (array) $role));
            return response()->json(['error' => 'Unauthorized: Invalid role'], 403);
        }

        return $next($request);
    }
}
