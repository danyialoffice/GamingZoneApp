<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PC extends Model
{
    use HasFactory, BelongsToTenant;
    
    protected $table = 'pcs';

    protected $fillable = [
        'tenant_id',
        'room_id',
        'name',
        'specs',
        'hourly_rate',
        'status',
        'ip_address',
        'mac_address',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_OFFLINE = 'offline';

    /**
     * Get the room this PC belongs to
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all bookings for this PC
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'pc_id');
    }

    /**
     * Get current booking
     */
    public function currentBooking(): ?Booking
    {
        return $this->bookings()
                    ->where('status', 'confirmed')
                    ->where('start_time', '<=', now())
                    ->where('end_time', '>=', now())
                    ->first();
    }

    /**
     * Check if PC is available
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Check if PC is occupied
     */
    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'available' => 'Available',
            'occupied' => 'Occupied',
            'maintenance' => 'Maintenance',
            'offline' => 'Offline',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get status color
     */
    public function getStatusColor(): string
    {
        $colors = [
            'available' => 'success',
            'occupied' => 'danger',
            'maintenance' => 'warning',
            'offline' => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Scope to available PCs
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope to in a specific room
     */
    public function scopeInRoom($query, int $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Check if available at specific time
     */
    public function isAvailableAt(\DateTime $start, \DateTime $end): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $conflict = $this->bookings()
                        ->where('status', 'confirmed')
                        ->where(function ($query) use ($start, $end) {
                            $query->whereBetween('start_time', [$start, $end])
                                  ->orWhereBetween('end_time', [$start, $end])
                                  ->orWhere(function ($q) use ($start, $end) {
                                      $q->where('start_time', '<=', $start)
                                        ->where('end_time', '>=', $end);
                                  });
                        })
                        ->exists();

        return !$conflict;
    }

    /**
     * Get specs as array
     */
    public function getSpecsArray(): array
    {
        return json_decode($this->specs, true) ?? [];
    }

    /**
     * Set specs from array
     */
    public function setSpecsFromArray(array $specs): void
    {
        $this->specs = json_encode($specs);
    }

    /**
     * Get formatted specs
     */
    public function getFormattedSpecs(): string
    {
        $specs = $this->getSpecsArray();
        
        if (empty($specs)) {
            return 'Standard Configuration';
        }

        $parts = [];
        
        if (isset($specs['cpu'])) $parts[] = 'CPU: ' . $specs['cpu'];
        if (isset($specs['gpu'])) $parts[] = 'GPU: ' . $specs['gpu'];
        if (isset($specs['ram'])) $parts[] = 'RAM: ' . $specs['ram'];
        if (isset($specs['storage'])) $parts[] = 'Storage: ' . $specs['storage'];
        
        return implode(' | ', $parts) ?: 'Standard Configuration';
    }
}
