<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PC;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingStatusController extends Controller
{
    /**
     * Display PC status dashboard
     */
    public function index(Request $request)
    {
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return redirect()->route('home');
        }

        // Get filter parameters
        $filterDate = $request->filter_date;
        $filterStart = $request->filter_start;
        $filterEnd = $request->filter_end;
        $filterHours = $request->filter_hours;
        
        // Calculate end time based on either duration or specific end time
        if ($filterStart && ($filterEnd || $filterHours)) {
            $filterStartDateTime = Carbon::parse($filterDate . ' ' . $filterStart);
            if ($filterHours) {
                $filterEndDateTime = $filterStartDateTime->copy()->addHours((int)$filterHours);
            } else {
                $filterEndDateTime = Carbon::parse($filterDate . ' ' . $filterEnd);
            }
        }

        // Get all rooms with their PCs
        $rooms = Room::where('status', 'active')
            ->with(['pcs' => function($query) {
                $query->orderBy('name');
            }])
            ->get();

        // Initialize PC statuses based on PC model status
        $pcStatuses = [];
        
        foreach ($rooms as $room) {
            foreach ($room->pcs as $pc) {
                // If time filter is active, check if PC is available during that time slot
                if ($filterStart && ($filterEnd || $filterHours)) {
                    $conflictingBooking = Booking::where('pc_id', $pc->id)
                        ->whereIn('status', ['temporary', 'pending', 'confirmed'])
                        ->where(function($query) use ($filterStartDateTime, $filterEndDateTime) {
                            // Booking overlaps with selected time slot
                            $query->where(function($q) use ($filterStartDateTime, $filterEndDateTime) {
                                $q->where('start_time', '<', $filterEndDateTime)
                                  ->where('end_time', '>', $filterStartDateTime);
                            });
                        })
                        ->first();
                    
                    if ($conflictingBooking) {
                        $isTemporary = $conflictingBooking->status === 'temporary';
                        $pcStatuses[$pc->id] = [
                            'status' => $isTemporary ? 'temporary' : 'booked',
                            'user' => $conflictingBooking->user->name ?? 'Unknown',
                            'end_time' => $conflictingBooking->end_time,
                            'minutes_remaining' => $filterStartDateTime->diffInMinutes($conflictingBooking->end_time, false)
                        ];
                    } elseif ($pc->status === 'maintenance') {
                        $pcStatuses[$pc->id] = [
                            'status' => 'maintenance',
                            'user' => 'Maintenance',
                            'end_time' => null,
                            'minutes_remaining' => null
                        ];
                    }
                    // If no conflict and PC is available, show as available (no entry needed)
                } else {
                    // Default behavior: check for any active booking (including temporary)
                    $activeBooking = Booking::where('pc_id', $pc->id)
                        ->whereIn('status', ['temporary', 'pending', 'confirmed'])
                        ->where('end_time', '>', Carbon::now())
                        ->first();
                    
                    if ($activeBooking) {
                        $isTemporary = $activeBooking->status === 'temporary';
                        $pcStatuses[$pc->id] = [
                            'status' => $isTemporary ? 'temporary' : 'booked',
                            'user' => $activeBooking->user->name ?? 'Unknown',
                            'end_time' => $activeBooking->end_time,
                            'minutes_remaining' => Carbon::now()->diffInMinutes($activeBooking->end_time, false)
                        ];
                    } elseif ($pc->status === 'maintenance') {
                        $pcStatuses[$pc->id] = [
                            'status' => 'maintenance',
                            'user' => 'Maintenance',
                            'end_time' => null,
                            'minutes_remaining' => null
                        ];
                    }
                }
            }
        }

        // Get view preference from session (default to 'grid')
        $viewMode = $request->session()->get('pc_status_view_mode', 'grid');

        return view('website.booking.pc-status', compact('rooms', 'pcStatuses', 'filterDate', 'filterStart', 'filterEnd', 'filterHours', 'viewMode'));
    }

    /**
     * Save view mode preference to session
     */
    public function setViewMode(Request $request)
    {
        $viewMode = $request->input('view_mode', 'grid');
        
        // Validate view mode
        if (!in_array($viewMode, ['grid', 'compact', 'list'])) {
            $viewMode = 'grid';
        }
        
        $request->session()->put('pc_status_view_mode', $viewMode);
        
        return response()->json(['success' => true, 'view_mode' => $viewMode]);
    }

    /**
     * Get available PCs grouped by room
     */
    public function getAvailablePcs()
    {
        $tenant = Tenant::current();
        
        $rooms = Room::where('status', 'active')
            ->with(['pcs' => function($query) {
                $query->where('status', 'available');
            }])
            ->get();

        // Filter out rooms with no available PCs
        $availableRooms = $rooms->filter(function($room) {
            return $room->pcs->count() > 0;
        });

        return response()->json([
            'rooms' => $availableRooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'pcs' => $room->pcs->map(function($pc) {
                        return [
                            'id' => $pc->id,
                            'name' => $pc->name,
                            'hourly_rate' => $pc->hourly_rate
                        ];
                    })
                ];
            })
        ]);
    }
}