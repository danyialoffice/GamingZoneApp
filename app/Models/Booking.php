<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'booking_group_id',
        'user_id',
        'room_id',
        'pc_id',
        'start_time',
        'end_time',
        'hours',
        'total_amount',
        'discount',
        'status',
        'approved_by',
        'approved_at',
        'expires_at',
        'notes',
        'admin_notes',
        'checked_in_at',
        'checked_out_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'hours' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the user who made the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the PC
     */
    public function pc(): BelongsTo
    {
        return $this->belongsTo(PC::class);
    }

    /**
     * Get the approver
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all related bookings in the same group
     */
    public function groupBookings()
    {
        return $this->hasMany(Booking::class, 'booking_group_id', 'booking_group_id')
                    ->where('id', '!=', $this->id);
    }

    /**
     * Get all bookings in this group (including this one)
     */
    public function getAllGroupBookings()
    {
        if (!$this->booking_group_id) {
            return collect([$this]);
        }
        return Booking::where('booking_group_id', $this->booking_group_id)->get();
    }

    /**
     * Get all PC names in this booking group
     */
    public function getGroupPcNames(): string
    {
        $allBookings = $this->getAllGroupBookings();
        $pcNames = $allBookings->map(function($booking) {
            return $booking->pc->name ?? 'Unknown';
        })->unique()->values();
        
        if ($pcNames->count() === 1) {
            return $pcNames->first();
        }
        
        return $pcNames->implode(', ') . ' (' . $pcNames->count() . ' PCs)';
    }

    /**
     * Get PC count for this booking group
     */
    public function getGroupPcCount(): int
    {
        return $this->getAllGroupBookings()->count();
    }

    /**
     * Get total amount for the entire booking group
     */
    public function getGroupTotalAmount(): float
    {
        return $this->getAllGroupBookings()->sum('total_amount');
    }

    /**
     * Check if booking has group members
     */
    public function hasGroupMembers(): bool
    {
        return $this->booking_group_id !== null && $this->getAllGroupBookings()->count() > 1;
    }

    /**
     * Scope to get booking groups (first booking in each group)
     */
    public function scopeBookingGroups($query)
    {
        return $query->whereNull('booking_group_id')
                     ->orWhereRaw('id = (SELECT MIN(id) FROM bookings b2 WHERE b2.booking_group_id = bookings.booking_group_id OR bookings.booking_group_id IS NULL)');
    }

    /**
     * Scope to get all bookings for a group
     */
    public function scopeForGroup($query, string $groupId)
    {
        return $query->where('booking_group_id', $groupId);
    }

    /**
     * Check if booking is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if booking is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if booking is expired
     */
    public function isExpired(): bool
    {
        if ($this->status === self::STATUS_EXPIRED) {
            return true;
        }

        // Check if pending or temporary and past expiration time
        if (($this->isPending() || $this->isTemporary()) && $this->expires_at && $this->expires_at->isPast()) {
            return true;
        }

        return false;
    }
    
    /**
     * Check if booking is temporary
     */
    public function isTemporary(): bool
    {
        return $this->status === 'temporary';
    }

    /**
     * Check if booking can be approved
     */
    public function canBeApproved(): bool
    {
        return ($this->isPending() || $this->isTemporary()) &&  !$this->isExpired();
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['temporary', self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Check if booking can be checked in
     */
    public function canCheckIn(): bool
    {
        return $this->isConfirmed() && !$this->checked_in_at;
    }

    /**
     * Check if booking can be checked out
     */
    public function canCheckOut(): bool
    {
        return $this->isConfirmed() && $this->checked_in_at && !$this->checked_out_at;
    }

    /**
     * Approve the booking
     */
    public function approve(User $approver): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        // Update PC status
        $this->pc->update(['status' => PC::STATUS_OCCUPIED]);

        // Create notification
        $this->createNotification('booking_approved', 'Booking Approved', 'Your booking has been approved!');

        return true;
    }

    /**
     * Reject the booking
     */
    public function reject(User $approver, ?string $reason = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'admin_notes' => $reason,
        ]);

        // Create notification
        $this->createNotification('booking_rejected', 'Booking Rejected', $reason ?? 'Your booking has been rejected.');

        return true;
    }

    /**
     * Cancel the booking
     */
    public function cancel(?string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'admin_notes' => $reason,
        ]);

        // Free up the PC if it was confirmed
        if ($this->isConfirmed()) {
            $this->pc->update(['status' => PC::STATUS_AVAILABLE]);
        }

        return true;
    }

    /**
     * Check in
     */
    public function checkIn(): bool
    {
        if (!$this->canCheckIn()) {
            return false;
        }

        $this->update(['checked_in_at' => now()]);

        return true;
    }

    /**
     * Check out
     */
    public function checkOut(): bool
    {
        if (!$this->canCheckOut()) {
            return false;
        }

        $this->update([
            'checked_out_at' => now(),
            'status' => self::STATUS_COMPLETED,
        ]);

        // Free up the PC
        $this->pc->update(['status' => PC::STATUS_AVAILABLE]);

        return true;
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): bool
    {
        if (!$this->isPending() && !$this->isTemporary()) {
            return false;
        }

        $this->update(['status' => self::STATUS_EXPIRED]);

        return true;
    }

    /**
     * Create notification for booking
     */
    public function createNotification(string $type, string $title, string $message): void
    {
        Notification::create([
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => [
                'booking_id' => $this->id,
                'room' => $this->room->name ?? null,
                'pc' => $this->pc->name ?? null,
                'start_time' => $this->start_time->toISOString(),
            ],
        ]);
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'temporary' => 'Temporary Hold',
            'pending' => 'Pending Approval',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get status color
     */
    public function getStatusColor(): string
    {
        $colors = [
            'temporary' => 'info',
            'pending' => 'warning',
            'confirmed' => 'success',
            'completed' => 'info',
            'expired' => 'secondary',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Calculate total amount
     */
    public function calculateTotal(): float
    {
        $pcRate = $this->pc->hourly_rate ?? 0;
        $roomRate = $this->room->hourly_rate ?? 0;
        
        $baseRate = max($pcRate, $roomRate);
        
        return $baseRate * $this->hours;
    }

    /**
     * Scope to pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope to today's bookings
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    /**
     * Scope to user's bookings
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get time remaining until booking starts
     */
    public function getTimeUntilStart(): ?string
    {
        if (!$this->start_time || $this->start_time->isPast()) {
            return null;
        }

        $diff = now()->diff($this->start_time);
        
        if ($diff->days > 0) {
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
        }
        
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }
        
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }
}
