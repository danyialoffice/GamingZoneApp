<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on user role
            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('super-admin.dashboard'));
            }

            // Try to detect tenant by current domain/subdomain
            $currentTenant = Tenant::current();
            
            // If user has tenant associations, redirect to tenant dashboard
            $tenantUsers = $user->tenantUsers()->with('tenant')->get();
            
            if ($tenantUsers->count() === 1) {
                // Only one tenant, set it and redirect
                $tenantUser = $tenantUsers->first();
                Tenant::setCurrent($tenantUser->tenant);
                
                // Redirect based on role
                if ($user->isPlayer()) {
                    return redirect()->intended(route('player.dashboard'));
                }
                return redirect()->intended(route('home'));
            }
            
            // Check if current tenant is in user's tenants
            if ($currentTenant && $tenantUsers->contains('tenant_id', $currentTenant->id)) {
                // Redirect based on role
                if ($user->isPlayer()) {
                    return redirect()->intended(route('player.dashboard'));
                }
                return redirect()->intended(route('home'));
            }
            
            // Multiple tenants or no current tenant, show tenant selection
            return redirect()->intended(route('select-tenant'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'nullable|string|max:20'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => 'active'
        ]);

        // If tenant_id provided, add user to that tenant as player
        if ($request->has('tenant_id')) {
            $tenant = Tenant::find($request->tenant_id);
            
            if ($tenant) {
                $playerRole = Role::where('tenant_id', $tenant->id)
                                 ->where('slug', 'player')
                                 ->first();

                if ($playerRole) {
                    TenantUser::create([
                        'tenant_id' => $tenant->id,
                        'user_id' => $user->id,
                        'role_id' => $playerRole->id,
                        'status' => 'active'
                    ]);

                    Tenant::setCurrent($tenant);
                }
            }
        }

        Auth::login($user);

        // Redirect based on role
        if ($user->isPlayer()) {
            return redirect()->route('player.dashboard')->with('success', 'Registration successful! Welcome to Gaming Zone');
        }
        return redirect()->route('tenant.dashboard')->with('success', 'Registration successful! Welcome to Gaming Zone');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Tenant::clearCurrent();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out');
    }

    /**
     * Select tenant
     */
    public function selectTenant()
    {
        $tenants = Tenant::where('status', 'active')->get();

        return view('auth.select-tenant', compact('tenants'));
    }

    /**
     * Set current tenant
     */
    public function setTenant(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id'
        ]);

        $user = auth()->user();
        $tenant = Tenant::find($request->tenant_id);

        // Check if user belongs to this tenant
        if (!$user->belongsToTenant($tenant->id)) {
            return redirect()->back()->with('error', 'You do not belong to this gaming zone');
        }

        Tenant::setCurrent($tenant);

        // Redirect based on role
        if (auth()->user()->isPlayer()) {
            return redirect()->route('player.dashboard');
        }
        return redirect()->route('home');
    }

    /**
     * Join a tenant (for users not yet associated)
     */
    public function joinTenant(Request $request, $tenantId)
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            return redirect()->back()->with('error', 'Gaming zone not found');
        }

        // Get or create player role for this tenant
        $playerRole = Role::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'player'],
            ['name' => 'Player', 'permissions' => json_encode(['booking.create', 'booking.view'])]
        );

        // Add user to tenant as player (or update if exists)
        TenantUser::updateOrCreate(
            ['tenant_id' => $tenant->id, 'user_id' => auth()->id()],
            ['role_id' => $playerRole->id, 'status' => 'active']
        );

        Tenant::setCurrent($tenant);

        // Redirect based on role
        if (auth()->user()->isPlayer()) {
            return redirect()->route('player.dashboard')->with('success', 'Welcome to ' . $tenant->name . '!');
        }
        return redirect()->route('home')->with('success', 'Welcome to ' . $tenant->name . '!');
    }
}
