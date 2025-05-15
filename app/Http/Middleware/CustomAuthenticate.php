<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class CustomAuthenticate
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        try {
            $this->authenticate($request, $guards);
            return $next($request);
        } catch (AuthenticationException $e) {
            return response()->json(['error' => 'Tidak terautentikasi'], 401);
        }
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth()->guard($guard)->check()) {
                return $this->auth()->shouldUse($guard);
            }
        }

        throw new AuthenticationException('Unauthenticated.', $guards);
    }

    protected function auth()
    {
        return auth();
    }
}
