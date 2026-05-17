c<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
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

        // Super admin can do everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check each permission
        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Forbidden',
                        'message' => "You do not have the '{$permission}' permission"
                    ], 403);
                }

                return redirect()->back()->with('error', 'You do not have permission to perform this action');
            }
        }

        return $next($request);
    }
}
