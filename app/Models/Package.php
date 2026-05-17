<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'price',
        'hours',
        'pc_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'hours' => 'decimal:2',
        'pc_count' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get hourly rate equivalent
     */
    public function getHourlyRate(): float
    {
        if ($this->hours <= 0) {
            return 0;
        }

        return $this->price / $this->hours;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDuration(): string
    {
        $hours = (float) $this->hours;
        
        if ($hours >= 24) {
            $days = floor($hours / 24);
            $remainingHours = $hours % 24;
            
            if ($remainingHours > 0) {
                return $days . ' day' . ($days > 1 ? 's' : '') . ' ' . $remainingHours . ' hour' . ($remainingHours > 1 ? 's' : '');
            }
            
            return $days . ' day' . ($days > 1 ? 's' : '');
        }
        
        if ($hours >= 1) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        
        $minutes = $hours * 60;
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }

    /**
     * Check if package is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope to active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to sorted order
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'ASC')->orderBy('price', 'ASC');
    }

    /**
     * Get price with currency symbol
     */
    public function getFormattedPrice(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Calculate savings compared to hourly rate
     */
    public function calculateSavings(float $hourlyRate): float
    {
        $regularPrice = $hourlyRate * $this->hours;
        
        return max(0, $regularPrice - $this->price);
    }

    /**
     * Get savings percentage
     */
    public function getSavingsPercentage(float $hourlyRate): float
    {
        $regularPrice = $hourlyRate * $this->hours;
        
        if ($regularPrice <= 0) {
            return 0;
        }

        $savings = $this->calculateSavings($hourlyRate);
        
        return ($savings / $regularPrice) * 100;
    }
}
