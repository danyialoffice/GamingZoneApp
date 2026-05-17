<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Package;
use App\Models\PC;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin (only if not exists)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gamingzone.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0100',
                'status' => 'active'
            ]
        );

        // Create Gaming Zone 1: Cyber Arena (idempotent)
        $tenant1 = $this->createTenant(
            'Cyber Arena Gaming',
            'cyber-arena-' . Str::random(6),
            'cyberarena-' . Str::random(4),
            'Premium gaming experience with the latest hardware',
            '123 Gaming Street, Los Angeles, CA 90001',
            '+1-555-0101',
            'info@cyberarena.com',
            'pro'
        );

        // Create Gaming Zone 2: Elite Gaming Hub (idempotent)
        $tenant2 = $this->createTenant(
            'Elite Gaming Hub',
            'elite-hub-' . Str::random(6),
            'elitehub-' . Str::random(4),
            'Professional esports training facility',
            '456 Esports Ave, New York, NY 10001',
            '+1-555-0102',
            'contact@elitehub.com',
            'basic'
        );

        // Create Gaming Zone 3: Pixel Paradise (idempotent)
        $tenant3 = $this->createTenant(
            'Pixel Paradise Gaming',
            'pixel-paradise-' . Str::random(6),
            'pixelparadise-' . Str::random(4),
            'Family-friendly gaming center',
            '789 Fun Lane, Chicago, IL 60601',
            '+1-555-0103',
            'hello@pixelfactory.com',
            'basic'
        );

        // Create demo users for each tenant
        $this->createTenantUsers($tenant1);
        $this->createTenantUsers($tenant2);
        $this->createTenantUsers($tenant3);

        $this->command->info('Tenant seeding completed successfully!');
    }

    /**
     * Create a tenant with rooms and PCs
     */
    protected function createTenant(
        string $name,
        string $slug,
        ?string $subdomain,
        string $description,
        string $address,
        string $phone,
        string $email,
        string $plan = 'basic'
    ): Tenant {
        $tenant = Tenant::create([
            'name' => $name,
            'slug' => $slug,
            'subdomain' => $subdomain,
            'description' => $description,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'subscription_plan' => $plan,
            'status' => 'active',
            'subscription_start' => now(),
            'subscription_end' => now()->addYear(),
            'max_rooms' => $plan === 'pro' ? 10 : 2,
            'max_pcs' => $plan === 'pro' ? 50 : 10,
            'max_staff' => $plan === 'pro' ? 15 : 3,
            'custom_color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
        ]);

        // Create roles for this tenant
        $this->createRoles($tenant);

        // Create rooms
        $rooms = $this->createRooms($tenant, $plan);

        // Create PCs for each room
        foreach ($rooms as $room) {
            $this->createPCs($room, rand(5, 10));
        }

        // Create packages
        $this->createPackages($tenant);

        // Create some demo bookings
        $this->createDemoBookings($tenant, $rooms);

        return $tenant;
    }

    /**
     * Create roles for tenant
     */
    protected function createRoles(Tenant $tenant): void
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
                    'bookings.view', 'bookings.approve', 'bookings.reject',
                    'bookings.manage', 'pcs.view', 'rooms.view'
                ]
            ],
            [
                'name' => 'Player',
                'slug' => 'player',
                'description' => 'Book PCs and manage personal bookings',
                'permissions' => [
                    'bookings.create', 'bookings.view.own',
                    'rooms.view', 'pcs.view', 'packages.view'
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

    /**
     * Create rooms for tenant
     */
    protected function createRooms(Tenant $tenant, string $plan): array
    {
        $roomData = [
            ['name' => 'VIP Lounge', 'hourly_rate' => 15.00, 'description' => 'Premium gaming experience with top-tier hardware'],
            ['name' => 'Pro Arena', 'hourly_rate' => 10.00, 'description' => 'Competitive gaming area for tournaments'],
            ['name' => 'Casual Zone', 'hourly_rate' => 5.00, 'description' => 'Relaxed gaming environment for casual players'],
        ];

        $rooms = [];
        $maxRooms = $plan === 'pro' ? 5 : 2;

        for ($i = 0; $i < $maxRooms; $i++) {
            $data = $roomData[$i % count($roomData)];
            $rooms[] = Room::create([
                'tenant_id' => $tenant->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'hourly_rate' => $data['hourly_rate'],
                'status' => 'active',
                'sort_order' => $i
            ]);
        }

        return $rooms;
    }

    /**
     * Create PCs for room
     */
    protected function createPCs(Room $room, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            PC::create([
                'tenant_id' => $room->tenant_id,
                'room_id' => $room->id,
                'name' => $room->name . ' PC-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'specs' => json_encode([
                    'cpu' => 'Intel Core i9-13900K',
                    'gpu' => 'NVIDIA RTX 4090',
                    'ram' => '64GB DDR5',
                    'storage' => '2TB NVMe SSD',
                    'monitor' => '360Hz 27" 4K',
                    'keyboard' => 'Mechanical RGB',
                    'mouse' => 'Gaming Pro Wireless',
                    'headset' => 'Surround Sound 7.1'
                ]),
                'hourly_rate' => $room->hourly_rate,
                'status' => 'available',
                'ip_address' => '192.168.' . $room->tenant_id . '.' . str_pad($i, 3, '0', STR_PAD_LEFT)
            ]);
        }
    }

    /**
     * Create packages for tenant
     */
    protected function createPackages(Tenant $tenant): void
    {
        $packages = [
            [
                'name' => 'Quick Session',
                'description' => 'Perfect for a quick gaming session',
                'price' => 5.00,
                'hours' => 1,
                'pc_count' => 1
            ],
            [
                'name' => 'Gaming Marathon',
                'description' => '3-hour gaming session',
                'price' => 12.00,
                'hours' => 3,
                'pc_count' => 1
            ],
            [
                'name' => 'Weekend Warrior',
                'description' => '5 hours of uninterrupted gaming',
                'price' => 20.00,
                'hours' => 5,
                'pc_count' => 1
            ],
            [
                'name' => 'Pro Package',
                'description' => 'Full day gaming experience',
                'price' => 35.00,
                'hours' => 10,
                'pc_count' => 1
            ],
        ];

        foreach ($packages as $index => $package) {
            Package::create([
                'tenant_id' => $tenant->id,
                'name' => $package['name'],
                'description' => $package['description'],
                'price' => $package['price'],
                'hours' => $package['hours'],
                'pc_count' => $package['pc_count'],
                'is_active' => true,
                'sort_order' => $index
            ]);
        }
    }

    /**
     * Create tenant users
     */
    protected function createTenantUsers(Tenant $tenant): void
    {
        // Admin user
        $admin = User::create([
            'name' => $tenant->name . ' Admin',
            'email' => 'admin@' . $tenant->slug . '.com',
            'password' => Hash::make('password'),
            'phone' => $tenant->phone,
            'status' => 'active'
        ]);

        $adminRole = Role::where('tenant_id', $tenant->id)->where('slug', 'tenant_admin')->first();
        TenantUser::create([
            'tenant_id' => $tenant->id,
            'user_id' => $admin->id,
            'role_id' => $adminRole->id,
            'status' => 'active'
        ]);

        // Booking Manager
        $manager = User::create([
            'name' => $tenant->name . ' Manager',
            'email' => 'manager@' . $tenant->slug . '.com',
            'password' => Hash::make('password'),
            'phone' => '+1-555-' . rand(1000, 9999),
            'status' => 'active'
        ]);

        $managerRole = Role::where('tenant_id', $tenant->id)->where('slug', 'booking_manager')->first();
        TenantUser::create([
            'tenant_id' => $tenant->id,
            'user_id' => $manager->id,
            'role_id' => $managerRole->id,
            'status' => 'active'
        ]);

        // Create 3 player accounts
        for ($i = 1; $i <= 3; $i++) {
            $player = User::create([
                'name' => 'Player ' . $i . ' (' . $tenant->name . ')',
                'email' => 'player' . $i . '@' . $tenant->slug . '.com',
                'password' => Hash::make('password'),
                'phone' => '+1-555-' . rand(1000, 9999),
                'status' => 'active'
            ]);

            $playerRole = Role::where('tenant_id', $tenant->id)->where('slug', 'player')->first();
            TenantUser::create([
                'tenant_id' => $tenant->id,
                'user_id' => $player->id,
                'role_id' => $playerRole->id,
                'status' => 'active'
            ]);
        }
    }

    /**
     * Create demo bookings
     */
    protected function createDemoBookings(Tenant $tenant, array $rooms): void
    {
        // Get players for this tenant
        $players = TenantUser::where('tenant_id', $tenant->id)
                           ->whereHas('role', fn($q) => $q->where('slug', 'player'))
                           ->with('user')
                           ->get();

        if ($players->isEmpty()) return;

        foreach ($rooms as $room) {
            $pcs = $room->pcs()->limit(3)->get();

            foreach ($pcs as $pc) {
                // Create a confirmed booking
                $player = $players->random();
                $startTime = now()->addDays(rand(1, 7))->setTime(rand(10, 20), 0);
                $hours = rand(1, 4);

                Booking::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $player->user_id,
                    'room_id' => $room->id,
                    'pc_id' => $pc->id,
                    'start_time' => $startTime,
                    'end_time' => $startTime->copy()->addHours($hours),
                    'hours' => $hours,
                    'total_amount' => $pc->hourly_rate * $hours,
                    'status' => 'confirmed',
                    'approved_by' => $players->random()->user_id ?? null,
                    'approved_at' => now()
                ]);
            }
        }
    }
}
