<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'address',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all tenants this user belongs to
     */
    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    /**
     * Get all tenants
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users')
                    ->withPivot('role_id', 'status', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get active tenant memberships
     */
    public function activeTenants(): BelongsToMany
    {
        return $this->tenants()->wherePivot('status', 'active');
    }

    /**
     * Check if user belongs to a specific tenant
     */
    public function belongsToTenant(int $tenantId): bool
    {
        return $this->tenantUsers()->where('tenant_id', $tenantId)->exists();
    }

    /**
     * Get role for a specific tenant
     */
    public function getRoleInTenant(int $tenantId): ?Role
    {
        $tenantUser = $this->tenantUsers()->where('tenant_id', $tenantId)->first();
        
        if (!$tenantUser) {
            return null;
        }

        return $tenantUser->role;
    }

    /**
     * Get role slug for a specific tenant
     */
    public function getRoleSlugInTenant(int $tenantId): ?string
    {
        $role = $this->getRoleInTenant($tenantId);
        
        return $role ? $role->slug : null;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->email === 'superadmin@gamingzone.com' || 
               $this->hasRole('super_admin');
    }

    /**
     * Check if user has a specific role in current tenant
     */
    public function hasRole(string $roleSlug): bool
    {
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return false;
        }

        $roleSlugInTenant = $this->getRoleSlugInTenant($tenant->id);
        
        return $roleSlugInTenant === $roleSlug;
    }

    /**
     * Check if user is tenant admin
     */
    public function isTenantAdmin(): bool
    {
        return $this->hasRole('tenant_admin');
    }

    /**
     * Check if user is booking manager
     */
    public function isBookingManager(): bool
    {
        return $this->hasRole('booking_manager');
    }

    /**
     * Check if user is player
     */
    public function isPlayer(): bool
    {
        return $this->hasRole('player');
    }

    /**
     * Get all bookings across all tenants
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get bookings for current tenant
     */
    public function tenantBookings(): HasMany
    {
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return $this->bookings();
        }

        return $this->bookings()->where('tenant_id', $tenant->id);
    }

    /**
     * Get notifications
     */
    public function notifications(): HasMany
    {
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return $this->hasMany(Notification::class);
        }

        return $this->hasMany(Notification::class)
                    ->where('tenant_id', $tenant->id);
    }

    /**
     * Get unread notifications
     */
    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->where('is_read', false);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return asset('images/default-avatar.png');
    }

    /**
     * Scope to active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get all permissions for current tenant
     */
    public function getPermissions(): array
    {
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return [];
        }

        $role = $this->getRoleInTenant($tenant->id);
        
        return $role ? ($role->permissions ?? []) : [];
    }

    /**
     * Check if has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->getPermissions();
        
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }
}
