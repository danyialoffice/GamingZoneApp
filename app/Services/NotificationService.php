<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notification type constants
     */
    const TYPE_BOOKING_CREATED = 'booking_created';
    const TYPE_BOOKING_APPROVED = 'booking_approved';
    const TYPE_BOOKING_REJECTED = 'booking_rejected';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_COMPLETED = 'booking_completed';
    const TYPE_BOOKING_CHECK_IN_REMINDER = 'booking_check_in_reminder';
    const TYPE_BOOKING_EXPIRING = 'booking_expiring';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_PAYMENT_REFUNDED = 'payment_refunded';
    const TYPE_PAYMENT_FAILED = 'payment_failed';
    const TYPE_PC_MAINTENANCE = 'pc_maintenance';
    const TYPE_PC_ONLINE = 'pc_online';
    const TYPE_SYSTEM = 'system';
    const TYPE_ACCOUNT_WELCOME = 'account_welcome';
    const TYPE_TENANT_JOINED = 'tenant_joined';
    const TYPE_TENANT_CREATED = 'tenant_created';

    /**
     * Create a notification for a single user
     */
    public static function create(
        int $userId,
        int $tenantId,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        bool $sendEmail = null
    ): ?Notification {
        try {
            $notification = Notification::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
            
            // Send email if enabled for this tenant
            $sendEmailFlag = $sendEmail ?? self::isEmailNotificationEnabled($tenantId, $type);
            if ($sendEmailFlag) {
                self::sendEmailNotification($userId, $tenantId, $title, $message, $type);
            }
            
            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if email notifications are enabled for a tenant
     */
    public static function isEmailNotificationEnabled(int $tenantId, string $type): bool
    {
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return false;
        }
        
        $settings = $tenant->settings ?? [];
        
        // Check if email notifications are globally enabled
        if (!($settings['email_notifications_enabled'] ?? false)) {
            return false;
        }
        
        // Check notification type settings
        $typeSettings = $settings['notification_types'] ?? [];
        
        // Map notification types to setting keys
        $typeMap = [
            self::TYPE_BOOKING_APPROVED => 'booking_approved',
            self::TYPE_BOOKING_REJECTED => 'booking_rejected',
            self::TYPE_BOOKING_CANCELLED => 'booking_cancelled',
            self::TYPE_BOOKING_CREATED => 'booking_created',
            self::TYPE_BOOKING_COMPLETED => 'booking_completed',
            self::TYPE_PAYMENT_RECEIVED => 'payment_received',
            self::TYPE_PAYMENT_REFUNDED => 'payment_refunded',
        ];
        
        $settingKey = $typeMap[$type] ?? null;
        
        // If specific type is set, use that; otherwise use default (enabled)
        if ($settingKey !== null && isset($typeSettings[$settingKey])) {
            return (bool) $typeSettings[$settingKey];
        }
        
        return true;
    }
    
    /**
     * Send email notification
     */
    public static function sendEmailNotification(int $userId, int $tenantId, string $title, string $message, string $type): void
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->email) {
                return;
            }
            
            // Queue email for sending (in production, use queue)
            Mail::send([], [], function($email) use ($user, $title, $message, $type) {
                $email->to($user->email, $user->name)
                      ->subject($title . ' - Gaming Zone')
                      ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <h2 style='color: #6f42c1;'>{$title}</h2>
                            <p style='color: #333; line-height: 1.6;'>{$message}</p>
                            <hr style='border: 1px solid #e2e8f0; margin: 20px 0;'>
                            <p style='color: #64748b; font-size: 12px;'>
                                This notification was sent by Gaming Zone. 
                                You can manage your notification preferences in your account settings.
                            </p>
                        </div>
                      ");
            });
            
            Log::info("Email notification sent to {$user->email} for {$type}");
        } catch (\Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Update tenant email notification settings
     */
    public static function updateTenantEmailSettings(int $tenantId, array $settings): void
    {
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return;
        }
        
        $currentSettings = $tenant->settings ?? [];
        $currentSettings['email_notifications_enabled'] = $settings['enabled'] ?? false;
        $currentSettings['notification_types'] = $settings['types'] ?? [];
        
        $tenant->settings = $currentSettings;
        $tenant->save();
    }

    /**
     * Create notifications for multiple users
     */
    public static function createForUsers(
        array $userIds,
        int $tenantId,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        foreach ($userIds as $userId) {
            self::create($userId, $tenantId, $type, $title, $message, $data);
        }
    }

    /**
     * Create notifications for users with specific roles in a tenant
     */
    public static function createForRole(
        int $tenantId,
        string $role,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        $userIds = User::whereHas('tenantUsers', function ($query) use ($tenantId, $role) {
            $query->where('tenant_id', $tenantId)
                  ->whereHas('role', function ($q) use ($role) {
                      $q->where('slug', $role);
                  });
        })->pluck('id')->toArray();

        self::createForUsers($userIds, $tenantId, $type, $title, $message, $data);
    }

    /**
     * Create notifications for all staff in a tenant
     */
    public static function createForStaff(
        int $tenantId,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        $userIds = User::whereHas('tenantUsers', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)
                  ->whereHas('role', function ($q) {
                      $q->whereIn('slug', ['admin', 'staff']);
                  });
        })->pluck('id')->toArray();

        self::createForUsers($userIds, $tenantId, $type, $title, $message, $data);
    }

    // ==================== BOOKING NOTIFICATIONS ====================

    /**
     * Notify user when booking is created
     */
    public static function bookingCreated(Booking $booking): void
    {
        $pc = $booking->pc;
        $room = $pc->room ?? null;
        $roomName = $room ? $room->name : 'Gaming Zone';
        
        // Notify the user who created the booking
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_CREATED,
            'Booking Submitted',
            "Your booking for {$pc->name} in {$roomName} has been submitted and is awaiting approval.",
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
                'pc_name' => $pc->name,
                'start_time' => $booking->start_time->toIsoString(),
                'end_time' => $booking->end_time->toIsoString(),
            ]
        );

        // Notify staff about new booking
        self::createForStaff(
            $booking->tenant_id,
            self::TYPE_BOOKING_CREATED,
            'New Booking Request',
            "New booking request from {$booking->user->name} for {$pc->name}. Please review.",
            [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'user_name' => $booking->user->name,
                'pc_id' => $pc->id,
            ]
        );
    }

    /**
     * Notify user when booking is approved
     */
    public static function bookingApproved(Booking $booking, ?int $approvedBy = null): void
    {
        $pc = $booking->pc;
        $room = $pc->room ?? null;
        $roomName = $room ? $room->name : 'Gaming Zone';
        $approvedByName = $approvedBy ? User::find($approvedBy)?->name : 'Administrator';
        
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_APPROVED,
            'Booking Approved! 🎉',
            "Your booking for {$pc->name} in {$roomName} has been approved! See you soon!",
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
                'approved_by' => $approvedBy,
                'start_time' => $booking->start_time->toIsoString(),
            ]
        );
    }

    /**
     * Notify user when booking is rejected
     */
    public static function bookingRejected(Booking $booking, string $reason = '', ?int $rejectedBy = null): void
    {
        $pc = $booking->pc;
        $rejectedByName = $rejectedBy ? User::find($rejectedBy)?->name : 'Administrator';
        
        $message = "Your booking for {$pc->name} has been rejected by {$rejectedByName}.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }
        
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_REJECTED,
            'Booking Rejected',
            $message,
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
                'rejected_by' => $rejectedBy,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Notify user when booking is cancelled
     */
    public static function bookingCancelled(Booking $booking, ?int $cancelledBy = null): void
    {
        $pc = $booking->pc;
        
        // Notify the user
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_CANCELLED,
            'Booking Cancelled',
            "Your booking for {$pc->name} has been cancelled.",
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
            ]
        );

        // If cancelled by staff, notify them too (if different from user)
        if ($cancelledBy && $cancelledBy !== $booking->user_id) {
            self::create(
                $cancelledBy,
                $booking->tenant_id,
                self::TYPE_BOOKING_CANCELLED,
                'Booking Cancelled',
                "You cancelled booking #{$booking->id} for {$pc->name}.",
                [
                    'booking_id' => $booking->id,
                    'cancelled_user_id' => $booking->user_id,
                ]
            );
        }
    }

    /**
     * Notify user when booking is completed
     */
    public static function bookingCompleted(Booking $booking): void
    {
        $pc = $booking->pc;
        
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_COMPLETED,
            'Booking Completed ✅',
            "Your session on {$pc->name} has been completed. Thank you for gaming with us!",
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
            ]
        );
    }

    /**
     * Send check-in reminder
     */
    public static function checkInReminder(Booking $booking): void
    {
        $pc = $booking->pc;
        $room = $pc->room ?? null;
        $roomName = $room ? $room->name : 'Gaming Zone';
        
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_CHECK_IN_REMINDER,
            '⏰ Time to Check In!',
            "Your booking at {$roomName} starts now! Head to PC {$pc->name} to begin your session.",
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
                'room_id' => $room?->id,
                'start_time' => $booking->start_time->toIsoString(),
            ]
        );
    }

    /**
     * Send expiring booking notification
     */
    public static function bookingExpiring(Booking $booking, int $minutesRemaining): void
    {
        $pc = $booking->pc;
        
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_EXPIRING,
            '⏳ Session Ending Soon',
            "Your session on {$pc->name} will end in {$minutesRemaining} minutes.",
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
                'minutes_remaining' => $minutesRemaining,
            ]
        );
    }

    /**
     * Notify when temporary booking is about to expire
     */
    public static function temporaryBookingExpiring(Booking $booking, int $minutesRemaining): void
    {
        $pc = $booking->pc;
        
        // Notify user
        self::create(
            $booking->user_id,
            $booking->tenant_id,
            self::TYPE_BOOKING_EXPIRING,
            '⚠️ Booking Expiring Soon!',
            "Your pending booking for {$pc->name} will be cancelled in {$minutesRemaining} minutes if not approved.",
            [
                'booking_id' => $booking->id,
                'pc_id' => $pc->id,
                'minutes_remaining' => $minutesRemaining,
            ]
        );

        // Notify staff
        self::createForStaff(
            $booking->tenant_id,
            self::TYPE_BOOKING_EXPIRING,
            '⚠️ Pending Booking Expiring',
            "Booking #{$booking->id} for {$pc->name} by {$booking->user->name} will expire in {$minutesRemaining} minutes!",
            [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
            ]
        );
    }

    // ==================== PAYMENT NOTIFICATIONS ====================

    /**
     * Notify user of payment received
     */
    public static function paymentReceived(int $userId, int $tenantId, float $amount, string $description): void
    {
        self::create(
            $userId,
            $tenantId,
            self::TYPE_PAYMENT_RECEIVED,
            'Payment Received 💰',
            "Payment of $" . number_format($amount, 2) . " received. {$description}",
            [
                'amount' => $amount,
                'description' => $description,
            ]
        );
    }

    /**
     * Notify user of refund
     */
    public static function paymentRefunded(int $userId, int $tenantId, float $amount, string $reason): void
    {
        self::create(
            $userId,
            $tenantId,
            self::TYPE_PAYMENT_REFUNDED,
            'Payment Refunded 💸',
            "A refund of $" . number_format($amount, 2) . " has been processed. {$reason}",
            [
                'amount' => $amount,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Notify user of payment failure
     */
    public static function paymentFailed(int $userId, int $tenantId, float $amount, string $reason): void
    {
        self::create(
            $userId,
            $tenantId,
            self::TYPE_PAYMENT_FAILED,
            'Payment Failed ❌',
            "Payment of $" . number_format($amount, 2) . " could not be processed. {$reason}",
            [
                'amount' => $amount,
                'reason' => $reason,
            ]
        );
    }

    // ==================== PC NOTIFICATIONS ====================

    /**
     * Notify users when PC goes to maintenance
     */
    public static function pcMaintenance(int $userId, int $tenantId, int $pcId, string $pcName, ?string $reason = null): void
    {
        $message = "PC {$pcName} has been scheduled for maintenance.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }
        
        self::create(
            $userId,
            $tenantId,
            self::TYPE_PC_MAINTENANCE,
            'PC Maintenance Scheduled',
            $message,
            [
                'pc_id' => $pcId,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Notify all staff when PC is set to maintenance
     */
    public static function pcMaintenanceScheduled(int $tenantId, int $pcId, string $pcName, ?string $reason = null): void
    {
        $message = "PC {$pcName} has been set to maintenance mode.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }
        
        self::createForStaff(
            $tenantId,
            self::TYPE_PC_MAINTENANCE,
            'PC Set to Maintenance',
            $message,
            [
                'pc_id' => $pcId,
                'pc_name' => $pcName,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Notify user when PC comes back online
     */
    public static function pcOnline(int $userId, int $tenantId, int $pcId, string $pcName): void
    {
        self::create(
            $userId,
            $tenantId,
            self::TYPE_PC_ONLINE,
            'PC Back Online! 🖥️',
            "PC {$pcName} is now available for booking.",
            [
                'pc_id' => $pcId,
                'pc_name' => $pcName,
            ]
        );
    }

    /**
     * Notify all staff when PC comes back online
     */
    public static function pcBackOnline(int $tenantId, int $pcId, string $pcName): void
    {
        self::createForStaff(
            $tenantId,
            self::TYPE_PC_ONLINE,
            'PC Back Online',
            "PC {$pcName} has been marked as available.",
            [
                'pc_id' => $pcId,
                'pc_name' => $pcName,
            ]
        );
    }

    // ==================== ACCOUNT NOTIFICATIONS ====================

    /**
     * Send welcome notification to new user
     */
    public static function welcomeUser(int $userId, int $tenantId, string $userName): void
    {
        self::create(
            $userId,
            $tenantId,
            self::TYPE_ACCOUNT_WELCOME,
            'Welcome to Gaming Zone! 🎮',
            "Welcome, {$userName}! We're excited to have you. Start by exploring available PCs and make your first booking!",
            [
                'user_name' => $userName,
            ]
        );
    }

    /**
     * Notify user when they join a tenant
     */
    public static function tenantJoined(int $userId, int $tenantId, string $tenantName): void
    {
        self::create(
            $userId,
            $tenantId,
            self::TYPE_TENANT_JOINED,
            'Joined ' . $tenantName,
            "You have successfully joined {$tenantName}. You can now access their gaming facilities.",
            [
                'tenant_name' => $tenantName,
            ]
        );
    }

    /**
     * Notify super admin when new tenant is created
     */
    public static function tenantCreated(int $tenantId, string $tenantName, string $ownerName): void
    {
        // Get super admin user IDs
        $superAdminIds = User::whereHas('roles', function ($query) {
            $query->where('slug', 'super_admin');
        })->pluck('id')->toArray();

        foreach ($superAdminIds as $adminId) {
            self::create(
                $adminId,
                $tenantId,
                self::TYPE_TENANT_CREATED,
                'New Gaming Zone Created! 🏢',
                "A new gaming zone '{$tenantName}' has been created by {$ownerName}.",
                [
                    'tenant_id' => $tenantId,
                    'tenant_name' => $tenantName,
                    'owner_name' => $ownerName,
                ]
            );
        }
    }

    // ==================== SYSTEM NOTIFICATIONS ====================

    /**
     * Send system notification to all staff
     */
    public static function systemNotification(int $tenantId, string $title, string $message, ?array $data = null): void
    {
        self::createForStaff(
            $tenantId,
            self::TYPE_SYSTEM,
            $title,
            $message,
            $data
        );
    }

    /**
     * Send maintenance announcement
     */
    public static function maintenanceAnnouncement(int $tenantId, string $message, ?string $startTime = null, ?string $endTime = null): void
    {
        $fullMessage = "🔧 Scheduled Maintenance: {$message}";
        if ($startTime) {
            $fullMessage .= " Starting: {$startTime}";
        }
        if ($endTime) {
            $fullMessage .= " Until: {$endTime}";
        }
        
        self::createForStaff(
            $tenantId,
            self::TYPE_SYSTEM,
            'Scheduled Maintenance',
            $fullMessage,
            [
                'message' => $message,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]
        );
    }

    /**
     * Get unread count for a user
     */
    public static function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)->unread()->count();
    }
}
