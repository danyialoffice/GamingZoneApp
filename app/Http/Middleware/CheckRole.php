<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'You must be logged in'
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Please login to continue');
        }

        // Super admin can access everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has required role in current tenant
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        return redirect()->back()->with('error', 'You do not have permission to access this page');
    }
}
