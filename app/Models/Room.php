<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'hourly_rate',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Get all PCs in this room
     */
    public function pcs(): HasMany
    {
        return $this->hasMany(PC::class);
    }

    /**
     * Get active PCs
     */
    public function activePcs(): HasMany
    {
        return $this->pcs()->where('status', 'available');
    }

    /**
     * Get all bookings for this room
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if room is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get total PCs count
     */
    public function getTotalPcsCount(): int
    {
        return $this->pcs()->count();
    }

    /**
     * Get available PCs count
     */
    public function getAvailablePcsCount(): int
    {
        return $this->pcs()->where('status', 'available')->count();
    }

    /**
     * Get PC image URL
     */
    public function getImageUrl(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/default-room.png');
    }

    /**
     * Scope to active rooms
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to sorted order
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
    }

    /**
     * Check availability for specific time
     */
    public function isAvailableAt(\DateTime $start, \DateTime $end): bool
    {
        $bookedPcs = $this->bookings()
                         ->where('status', 'confirmed')
                         ->where(function ($query) use ($start, $end) {
                             $query->whereBetween('start_time', [$start, $end])
                                   ->orWhereBetween('end_time', [$start, $end])
                                   ->orWhere(function ($q) use ($start, $end) {
                                       $q->where('start_time', '<=', $start)
                                         ->where('end_time', '>=', $end);
                                   });
                         })
                         ->count();

        $totalPcs = $this->getTotalPcsCount();

        return $bookedPcs < $totalPcs;
    }
}
