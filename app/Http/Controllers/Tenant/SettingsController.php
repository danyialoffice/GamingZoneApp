<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display notification settings page
     */
    public function notifications()
    {
        $tenant = Tenant::current();
        $settings = $tenant->settings ?? [];
        
        $emailEnabled = $settings['email_notifications_enabled'] ?? false;
        $notificationTypes = $settings['notification_types'] ?? [];
        
        return view('tenant.settings.notifications', compact(
            'tenant',
            'emailEnabled',
            'notificationTypes'
        ));
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $tenant = Tenant::current();
        
        $request->validate([
            'email_enabled' => 'nullable|boolean',
            'notification_types' => 'nullable|array',
            'notification_types.*' => 'nullable|boolean',
        ]);

        $settings = $tenant->settings ?? [];
        $settings['email_notifications_enabled'] = $request->boolean('email_enabled');
        $settings['notification_types'] = $request->input('notification_types', []);
        
        $tenant->settings = $settings;
        $tenant->save();

        return redirect()->back()
                       ->with('success', 'Notification settings updated successfully');
    }
}
