<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for super admin routes
        if ($request->is('super-admin/*') || $request->is('api/super-admin/*')) {
            return $next($request);
        }

        // Skip for auth routes
        if ($request->is('auth/*') || $request->is('api/auth/*')) {
            return $next($request);
        }

        // Skip for public website routes
        if ($request->is('/') || $request->is('zones/*') || $request->is('api/zones/*')) {
            return $next($request);
        }

        // Try to resolve tenant from subdomain or session
        $tenant = $this->resolveTenant($request);

        if (!$tenant) {
            // If no tenant found, redirect to zone selection or home
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Tenant not found',
                    'message' => 'Please specify a gaming zone'
                ], 404);
            }

            return redirect()->route('home')->with('error', 'Please select a gaming zone');
        }

        // Set current tenant
        Tenant::setCurrent($tenant);

        // Check if subscription is active
        if (!$tenant->isSubscriptionActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Subscription inactive',
                    'message' => 'This gaming zone subscription is not active'
                ], 403);
            }

            return redirect()->route('home')->with('error', 'This gaming zone subscription is not active');
        }

        // Share tenant with views
        view()->share('tenant', $tenant);

        return $next($request);
    }

    /**
     * Resolve tenant from request
     */
    protected function resolveTenant(Request $request): ?Tenant
    {
        // Check session first
        if (session()->has('tenant_id')) {
            return Tenant::find(session('tenant_id'));
        }

        // Check subdomain
        $host = $request->getHost();
        $subdomain = $this->getSubdomain($host);

        if ($subdomain && $subdomain !== 'www') {
            return Tenant::where('subdomain', $subdomain)
                        ->where('status', 'active')
                        ->first();
        }

        // Check query parameter (for development)
        if ($request->has('tenant_id')) {
            return Tenant::find($request->tenant_id);
        }

        return null;
    }

    /**
     * Extract subdomain from host
     */
    protected function getSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);
        
        // If we have subdomain.domain.com
        if (count($parts) >= 3) {
            return $parts[0];
        }

        return null;
    }
}
