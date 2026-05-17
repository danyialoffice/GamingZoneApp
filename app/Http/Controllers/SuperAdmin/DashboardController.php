<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display super admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'trial_tenants' => Tenant::where('status', 'trial')->count(),
            'total_users' => User::count(),
            'total_bookings' => Booking::withoutGlobalScopes()->count(),
            'monthly_revenue' => Booking::withoutGlobalScopes()
                                      ->whereMonth('created_at', now()->month)
                                      ->where('status', 'completed')
                                      ->sum('total_amount'),
        ];

        $recentTenants = Tenant::withoutGlobalScopes()
                              ->orderBy('created_at', 'desc')
                              ->limit(10)
                              ->get();

        $recentBookings = Booking::withoutGlobalScopes()
                                ->with(['tenant', 'user', 'pc'])
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

        $topTenants = Tenant::withoutGlobalScopes()
                           ->withCount(['bookings' => function ($query) {
                               $query->whereMonth('created_at', now()->month);
                           }])
                           ->orderBy('bookings_count', 'desc')
                           ->limit(5)
                           ->get();

        return view('super-admin.dashboard', compact('stats', 'recentTenants', 'recentBookings', 'topTenants'));
    }
}
