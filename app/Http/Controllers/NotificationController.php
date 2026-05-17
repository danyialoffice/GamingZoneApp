<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Tenant;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notifications list
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return redirect()->route('home');
        }

        // Get filter type
        $filterType = $request->get('type', 'all');
        
        // Build query
        $query = Notification::where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc');

        if ($filterType === 'unread') {
            $query->unread();
        } elseif ($filterType === 'read') {
            $query->read();
        } elseif ($filterType !== 'all') {
            $query->where('type', $filterType);
        }

        $notifications = $query->paginate(20);

        // Get unread count
        $unreadCount = NotificationService::getUnreadCount($user->id);

        // Get notification type stats
        $typeStats = [
            'all' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->unread()->count(),
            'read' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->read()->count(),
            'booking_created' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->where('type', NotificationService::TYPE_BOOKING_CREATED)->count(),
            'booking_approved' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->where('type', NotificationService::TYPE_BOOKING_APPROVED)->count(),
            'booking_rejected' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->where('type', NotificationService::TYPE_BOOKING_REJECTED)->count(),
            'booking_cancelled' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->where('type', NotificationService::TYPE_BOOKING_CANCELLED)->count(),
            'payment_received' => Notification::where('user_id', $user->id)->where('tenant_id', $tenant->id)->where('type', NotificationService::TYPE_PAYMENT_RECEIVED)->count(),
        ];

        return view('notifications.index', compact('notifications', 'unreadCount', 'filterType', 'typeStats'));
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'No tenant selected'], 400);
        }

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => NotificationService::getUnreadCount($user->id)
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'No tenant selected'], 400);
        }

        Notification::where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'No tenant selected'], 400);
        }

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'unread_count' => NotificationService::getUnreadCount($user->id)
        ]);
    }

    /**
     * Delete all read notifications
     */
    public function destroyAllRead()
    {
        $user = Auth::user();
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'No tenant selected'], 400);
        }

        Notification::where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->where('is_read', true)
            ->delete();

        return response()->json([
            'success' => true,
            'unread_count' => NotificationService::getUnreadCount($user->id)
        ]);
    }

    /**
     * Get unread count (API endpoint)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        // If user has a current tenant, get tenant-specific count
        $tenant = Tenant::current();
        if ($tenant) {
            $count = Notification::where('user_id', $user->id)
                ->where('tenant_id', $tenant->id)
                ->unread()
                ->count();
        } else {
            // Get all unread notifications across all tenants
            $count = Notification::where('user_id', $user->id)
                ->unread()
                ->count();
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for dropdown (API endpoint)
     */
    public function getRecent()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['notifications' => []]);
        }

        $tenant = Tenant::current();
        $query = Notification::where('user_id', $user->id);

        if ($tenant) {
            $query->where('tenant_id', $tenant->id);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'time_ago' => $notification->getTimeAgo(),
                    'icon' => $notification->getIcon(),
                    'color' => $notification->getColor(),
                    'data' => $notification->data,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => NotificationService::getUnreadCount($user->id)
        ]);
    }

    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        $user = Auth::user();
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'No tenant selected'], 400);
        }

        Notification::where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->delete();

        return response()->json(['success' => true]);
    }
}
