<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'logo',
        'custom_color',
        'description',
        'address',
        'phone',
        'email',
        'subscription_plan',
        'status',
        'subscription_start',
        'subscription_end',
        'max_rooms',
        'max_pcs',
        'max_staff',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'subscription_start' => 'date',
        'subscription_end' => 'date',
    ];

    /**
     * Current tenant holder
     */
    protected static ?Tenant $current = null;

    /**
     * Get the current tenant
     */
    public static function current(): ?self
    {
        if (self::$current) {
            return self::$current;
        }

        // Check session first
        if (session()->has('tenant_id')) {
            return self::$current = self::find(session('tenant_id'));
        }

        // Check subdomain
        $host = request()->getHost();
        $subdomain = explode('.', $host)[0] ?? null;
        
        if ($subdomain && $subdomain !== 'www') {
            return self::$current = self::where('subdomain', $subdomain)->first();
        }

        return null;
    }

    /**
     * Set current tenant
     */
    public static function setCurrent(?Tenant $tenant): void
    {
        self::$current = $tenant;
        
        if ($tenant) {
            session(['tenant_id' => $tenant->id]);
        } else {
            session()->forget('tenant_id');
        }
    }

    /**
     * Clear current tenant
     */
    public static function clearCurrent(): void
    {
        self::$current = null;
        session()->forget('tenant_id');
    }

    /**
     * Relationships
     */
    public function users(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function pcs(): HasMany
    {
        return $this->hasMany(PC::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if subscription is active
     */
    public function isSubscriptionActive(): bool
    {
        if ($this->status !== 'active' && $this->status !== 'trial') {
            return false;
        }

        if ($this->subscription_end) {
            return $this->subscription_end->isFuture();
        }

        return true;
    }

    /**
     * Get plan limits
     */
    public function getLimits(): array
    {
        $limits = [
            'basic' => ['rooms' => 2, 'pcs' => 10, 'staff' => 3],
            'pro' => ['rooms' => 10, 'pcs' => 50, 'staff' => 15],
            'enterprise' => ['rooms' => 9999, 'pcs' => 9999, 'staff' => 9999],
        ];

        return $limits[$this->subscription_plan] ?? $limits['basic'];
    }

    /**
     * Check if can add more rooms
     */
    public function canAddRoom(): bool
    {
        $limits = $this->getLimits();
        if ($limits['rooms'] === -1) return true;
        
        return $this->rooms()->count() < $limits['rooms'];
    }

    /**
     * Check if can add more PCs
     */
    public function canAddPC(): bool
    {
        $limits = $this->getLimits();
        if ($limits['pcs'] === -1) return true;
        
        return $this->pcs()->count() < $limits['pcs'];
    }

    /**
     * Cache key generator
     */
    public function cacheKey(string $key): string
    {
        return "tenant_{$this->id}_{$key}";
    }

    /**
     * Get setting value
     */
    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set setting value
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Get theme color
     */
    public function getThemeColor(): string
    {
        return $this->custom_color ?? '#6366f1';
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return asset('storage/' . $this->logo);
    }
}
