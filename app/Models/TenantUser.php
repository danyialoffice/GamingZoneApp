<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantUser extends Pivot
{
    protected $table = 'tenant_users';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role_id',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Get the tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user is active in this tenant
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('tenant_admin') || $this->hasRole('super_admin');
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
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
}
