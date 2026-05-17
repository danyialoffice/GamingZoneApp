<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PC;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display tenant dashboard
     */
    public function index()
    {
        $tenant = Tenant::current();

        if (!$tenant) {
            return redirect()->route('home');
        }

        $stats = [
            'total_rooms' => $tenant->rooms()->count(),
            'active_rooms' => $tenant->rooms()->where('status', 'active')->count(),
            'total_pcs' => $tenant->pcs()->count(),
            'available_pcs' => $tenant->pcs()->where('status', 'available')->count(),
            'today_bookings' => Booking::whereDate('start_time', today())->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'today_revenue' => Booking::whereDate('created_at', today())
                                    ->where('status', 'completed')
                                    ->sum('total_amount'),
            'month_revenue' => Booking::whereMonth('created_at', now()->month)
                                    ->where('status', 'completed')
                                    ->sum('total_amount'),
        ];

        // Get grouped bookings - first booking from each group
        $todaySubQuery = Booking::selectRaw('MIN(id) as min_id')
                               ->whereDate('start_time', today())
                               ->groupBy('booking_group_id');
        
        $todayBookings = Booking::with(['user', 'pc', 'room', 'groupBookings.pc', 'groupBookings.room'])
                               ->whereDate('start_time', today())
                               ->whereIn('id', $todaySubQuery)
                               ->orderBy('start_time')
                               ->limit(10)
                               ->get();

        // Get grouped pending bookings
        $pendingSubQuery = Booking::selectRaw('MIN(id) as min_id')
                                 ->where(function($q) {
                                     $q->where('status', 'pending')
                                       ->orWhere('status', 'temporary');
                                 })
                                 ->groupBy('booking_group_id');
        
        $pendingBookings = Booking::with(['user', 'pc', 'room', 'groupBookings.pc', 'groupBookings.room'])
                                 ->where(function($q) {
                                     $q->where('status', 'pending')
                                       ->orWhere('status', 'temporary');
                                 })
                                 ->whereIn('id', $pendingSubQuery)
                                 ->orderBy('created_at', 'asc')
                                 ->limit(5)
                                 ->get();

        return view('tenant.dashboard', compact('tenant', 'stats', 'todayBookings', 'pendingBookings'));
    }
}
