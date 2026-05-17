<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'booking_id',
        'user_id',
        'amount',
        'discount',
        'final_amount',
        'payment_method',
        'transaction_id',
        'status',
        'card_last_four',
        'payment_details',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_FAILED = 'failed';

    // Payment methods
    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_ONLINE = 'online';
    const METHOD_WALLET = 'wallet';

    /**
     * Get the booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): bool
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'paid_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark as refunded
     */
    public function markAsRefunded(): bool
    {
        $this->update([
            'status' => self::STATUS_REFUNDED,
            'refunded_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(): bool
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);

        return true;
    }

    /**
     * Get payment method label
     */
    public function getMethodLabel(): string
    {
        $labels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'online' => 'Online',
            'wallet' => 'Wallet',
        ];

        return $labels[$this->payment_method] ?? 'Unknown';
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'refunded' => 'Refunded',
            'failed' => 'Failed',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get status color
     */
    public function getStatusColor(): string
    {
        $colors = [
            'pending' => 'warning',
            'completed' => 'success',
            'refunded' => 'info',
            'failed' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->final_amount, 2);
    }

    /**
     * Scope to completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to user's payments
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to today's payments
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
