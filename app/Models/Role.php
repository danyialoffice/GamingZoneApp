<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Default permissions for each role
     */
    public static $defaultPermissions = [
        'super_admin' => ['*'],
        'tenant_admin' => [
            'rooms.manage',
            'pcs.manage',
            'bookings.manage',
            'bookings.approve',
            'bookings.reject',
            'users.manage',
            'staff.manage',
            'packages.manage',
            'payments.manage',
            'settings.manage',
            'reports.view',
        ],
        'booking_manager' => [
            'bookings.view',
            'bookings.approve',
            'bookings.reject',
            'bookings.manage',
            'pcs.view',
            'rooms.view',
        ],
        'player' => [
            'bookings.create',
            'bookings.view.own',
            'rooms.view',
            'pcs.view',
            'packages.view',
        ],
    ];

    /**
     * Get all users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    /**
     * Check if role has permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        
        // Check for wildcard
        if (in_array('*', $permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Set permissions from default
     */
    public function setDefaultPermissions(): void
    {
        if (isset(self::$defaultPermissions[$this->slug])) {
            $this->permissions = self::$defaultPermissions[$this->slug];
        }
    }

    /**
     * Scope to find by slug
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Get available permissions list
     */
    public static function getAvailablePermissions(): array
    {
        return [
            // Room permissions
            'rooms.view',
            'rooms.manage',
            'rooms.create',
            'rooms.edit',
            'rooms.delete',
            
            // PC permissions
            'pcs.view',
            'pcs.manage',
            'pcs.create',
            'pcs.edit',
            'pcs.delete',
            
            // Booking permissions
            'bookings.view',
            'bookings.view.own',
            'bookings.create',
            'bookings.manage',
            'bookings.approve',
            'bookings.reject',
            'bookings.cancel',
            
            // User permissions
            'users.view',
            'users.manage',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Staff permissions
            'staff.manage',
            'staff.view',
            
            // Package permissions
            'packages.view',
            'packages.manage',
            'packages.create',
            'packages.edit',
            'packages.delete',
            
            // Payment permissions
            'payments.view',
            'payments.manage',
            'payments.process',
            
            // Settings permissions
            'settings.view',
            'settings.manage',
            
            // Reports permissions
            'reports.view',
            'reports.export',
            
            // Notifications
            'notifications.view',
            'notifications.manage',
        ];
    }
}
