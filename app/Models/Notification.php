<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Get icon based on type
     */
    public function getIcon(): string
    {
        $icons = [
            'booking_created' => 'fa-calendar-plus',
            'booking_approved' => 'fa-check-circle',
            'booking_rejected' => 'fa-times-circle',
            'booking_cancelled' => 'fa-ban',
            'booking_completed' => 'fa-flag-checkered',
            'payment_received' => 'fa-dollar-sign',
            'payment_refunded' => 'fa-undo',
            'system' => 'fa-cog',
        ];

        return $icons[$this->type] ?? 'fa-bell';
    }

    /**
     * Get color based on type
     */
    public function getColor(): string
    {
        $colors = [
            'booking_created' => 'info',
            'booking_approved' => 'success',
            'booking_rejected' => 'danger',
            'booking_cancelled' => 'warning',
            'booking_completed' => 'primary',
            'payment_received' => 'success',
            'payment_refunded' => 'warning',
            'system' => 'secondary',
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    /**
     * Scope to unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get time ago
     */
    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create a notification
     */
    public static function createNotification(
        int $tenantId,
        int $userId,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): self {
        return self::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
