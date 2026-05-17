<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Display list of tenants
     */
    public function index(Request $request)
    {
        $query = Tenant::withoutGlobalScopes();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tenants = $query->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('super-admin.tenants.index', compact('tenants'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('super-admin.tenants.create');
    }

    /**
     * Store new tenant
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subscription_plan' => 'required|in:basic,pro,enterprise',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => ['required', 'min:8'],
        ]);

        DB::beginTransaction();

        try {
            // Create tenant
            $tenant = Tenant::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(6),
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'subscription_plan' => $request->subscription_plan,
                'status' => 'trial',
                'subscription_start' => now(),
                'subscription_end' => now()->addDays(14),
                'max_rooms' => $this->getPlanLimit($request->subscription_plan, 'rooms'),
                'max_pcs' => $this->getPlanLimit($request->subscription_plan, 'pcs'),
                'max_staff' => $this->getPlanLimit($request->subscription_plan, 'staff'),
            ]);

            // Create roles for this tenant
            $this->createTenantRoles($tenant);

            // Create tenant admin user
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => bcrypt($request->admin_password),
                'status' => 'active'
            ]);

            // Assign admin role to user
            $adminRole = Role::where('tenant_id', $tenant->id)
                            ->where('slug', 'tenant_admin')
                            ->first();

            TenantUser::create([
                'tenant_id' => $tenant->id,
                'user_id' => $admin->id,
                'role_id' => $adminRole->id,
                'status' => 'active'
            ]);

            DB::commit();

            return redirect()->route('super-admin.tenants.index')
                           ->with('success', 'Tenant created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create tenant: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display tenant details
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['rooms', 'pcs', 'users.user', 'bookings' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('super-admin.tenants.show', compact('tenant'));
    }

    /**
     * Show edit form
     */
    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update tenant
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subscription_plan' => 'required|in:basic,pro,enterprise',
            'status' => 'required|in:active,inactive,trial,suspended',
        ]);

        $tenant->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'subscription_plan' => $request->subscription_plan,
            'status' => $request->status,
            'max_rooms' => $this->getPlanLimit($request->subscription_plan, 'rooms'),
            'max_pcs' => $this->getPlanLimit($request->subscription_plan, 'pcs'),
            'max_staff' => $this->getPlanLimit($request->subscription_plan, 'staff'),
        ]);

        return redirect()->route('super-admin.tenants.show', $tenant->id)
                       ->with('success', 'Tenant updated successfully');
    }

    /**
     * Delete tenant
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('super-admin.tenants.index')
                       ->with('success', 'Tenant deleted successfully');
    }

    /**
     * Get plan limits
     */
    protected function getPlanLimit(string $plan, string $type): int
    {
        $limits = [
            'basic' => ['rooms' => 2, 'pcs' => 10, 'staff' => 3],
            'pro' => ['rooms' => 10, 'pcs' => 50, 'staff' => 15],
            'enterprise' => ['rooms' => -1, 'pcs' => -1, 'staff' => -1],
        ];

        return $limits[$plan][$type];
    }

    /**
     * Create default roles for new tenant
     */
    protected function createTenantRoles(Tenant $tenant): void
    {
        $roles = [
            [
                'name' => 'Tenant Admin',
                'slug' => 'tenant_admin',
                'description' => 'Full access to manage the gaming zone',
                'permissions' => ['*']
            ],
            [
                'name' => 'Booking Manager',
                'slug' => 'booking_manager',
                'description' => 'Manage bookings and reservations',
                'permissions' => [
                    'bookings.view',
                    'bookings.approve',
                    'bookings.reject',
                    'bookings.manage',
                    'pcs.view',
                    'rooms.view',
                ]
            ],
            [
                'name' => 'Player',
                'slug' => 'player',
                'description' => 'Book PCs and manage personal bookings',
                'permissions' => [
                    'bookings.create',
                    'bookings.view.own',
                    'packages.view',
                ]
            ],
        ];

        foreach ($roles as $role) {
            Role::create([
                'tenant_id' => $tenant->id,
                'name' => $role['name'],
                'slug' => $role['slug'],
                'description' => $role['description'],
                'permissions' => $role['permissions']
            ]);
        }
    }
}
