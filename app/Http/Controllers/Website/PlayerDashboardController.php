<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlayerDashboardController extends Controller
{
    /**
     * Display player dashboard with stats
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Get all bookings for the user (grouped by booking_group_id)
        $subQuery = Booking::selectRaw('MIN(id) as min_id')
                          ->where('user_id', $userId)
                          ->groupBy('booking_group_id');
        
        $allBookings = Booking::where('user_id', $userId)
                              ->whereIn('id', $subQuery)
                              ->get();
        
        // Total hours played (from completed and confirmed bookings)
        $totalHours = Booking::where('user_id', $userId)
                             ->whereIn('status', ['confirmed', 'completed'])
                             ->sum('hours');
        
        // Total booking groups
        $totalBookingGroups = $allBookings->count();
        
        // Total amount paid (from payments)
        $totalPaid = Payment::whereHas('booking', function($query) use ($userId) {
                              $query->where('user_id', $userId);
                          })
                          ->where('status', 'completed')
                          ->sum('amount');
        
        // Upcoming bookings (future bookings that are confirmed or pending approval)
        $upcomingBookings = Booking::where('user_id', $userId)
                                    ->whereIn('status', ['pending', 'confirmed'])
                                    ->where('start_time', '>', Carbon::now())
                                    ->orderBy('start_time')
                                    ->limit(5)
                                    ->get();
        
        // Recent booking groups (last 5)
        $recentBookings = $allBookings->sortByDesc('created_at')->take(5);
        
        // This month stats
        $monthStart = Carbon::now()->startOfMonth();
        $thisMonthHours = Booking::where('user_id', $userId)
                                  ->whereIn('status', ['confirmed', 'completed'])
                                  ->where('start_time', '>=', $monthStart)
                                  ->sum('hours');
        
        $thisMonthSpent = Payment::whereHas('booking', function($query) use ($userId, $monthStart) {
                                  $query->where('user_id', $userId)
                                        ->where('start_time', '>=', $monthStart);
                              })
                              ->where('status', 'completed')
                              ->sum('amount');
        
        $thisMonthBookings = Booking::where('user_id', $userId)
                                     ->whereIn('status', ['pending', 'confirmed'])
                                     ->where('created_at', '>=', $monthStart)
                                     ->count();

        return view('website.player.dashboard', compact(
            'totalHours',
            'totalBookingGroups', 
            'totalPaid',
            'upcomingBookings',
            'recentBookings',
            'thisMonthHours',
            'thisMonthSpent',
            'thisMonthBookings'
        ));
    }
}