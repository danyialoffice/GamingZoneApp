<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PC;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlayerBookingController extends Controller
{
    /**
     * Display booking form
     */
    public function create(Request $request)
    {
        $tenant = Tenant::current();
        $rooms = Room::where('status', 'active')->get();
        
        // Handle single or multiple PC selection
        $selectedPcs = collect();
        
        if ($request->has('pc_id')) {
            $pc = PC::find($request->pc_id);
            if ($pc) {
                $selectedPcs->push($pc);
            }
        }
        
        if ($request->has('pc_ids')) {
            $pcIds = explode(',', $request->pc_ids);
            $selectedPcs = PC::whereIn('id', $pcIds)->get();
        }

        // Pre-fill values from query params (passed from pc-status page)
        $prefilledDate = $request->get('date', date('Y-m-d'));
        $prefilledStartTime = $request->get('start_time', '');
        $prefilledHours = $request->get('hours', '');
        $prefilledEndTime = $request->get('end_time', '');

        return view('website.booking.create', compact('rooms', 'selectedPcs', 'prefilledDate', 'prefilledStartTime', 'prefilledHours', 'prefilledEndTime'));
    }

    /**
     * Store new booking
     */
    public function store(Request $request)
    {
        $tenant = Tenant::current();
        
        if (!$tenant) {
            return back()->with('error', 'Please select a gaming zone first.');
        }
        
        $request->validate([
            'pc_ids' => 'required',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'hours' => 'required|integer|min:1|max:12',
        ]);

        // Parse PC IDs (could be JSON array or comma-separated)
        $pcIds = is_string($request->pc_ids) ? json_decode($request->pc_ids, true) : $request->pc_ids;
        if (!is_array($pcIds)) {
            $pcIds = explode(',', $request->pc_ids);
        }
        
        $hours = (int) $request->hours;
        $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endDateTime = $startDateTime->copy()->addHours($hours);
        
        // Group PCs by room
        $pcsByRoom = [];
        foreach ($pcIds as $pcId) {
            $pc = PC::find((int) $pcId);
            if ($pc) {
                $roomId = $pc->room_id;
                if (!isset($pcsByRoom[$roomId])) {
                    $pcsByRoom[$roomId] = ['room' => $pc->room, 'pcs' => []];
                }
                $pcsByRoom[$roomId]['pcs'][] = $pc;
            }
        }
        
        // Validate and create bookings for each room
        $allBookings = [];
        
        // Generate a short, easy-to-find booking group ID (2 letters + 5 digits)
        $prefix = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)); // Random 2 uppercase letters
        $bookingGroupId = $prefix . str_pad(mt_rand(1, 99999), 4, '0', STR_PAD_LEFT);
        
        foreach ($pcsByRoom as $roomId => $roomData) {
            $room = $roomData['room'];
            $pcs = $roomData['pcs'];
            $hourlyRate = $room->hourly_rate;
            
            // Validate all PCs in this room - check for conflicting bookings
            foreach ($pcs as $pc) {
                // Check if PC already has a booking that overlaps with the requested time slot
                $conflictingBooking = Booking::where('pc_id', $pc->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('start_time', '<', $endDateTime)
                    ->where('end_time', '>', $startDateTime)
                    ->first();
                
                if ($conflictingBooking) {
                    $existingUser = $conflictingBooking->user->name ?? 'Another user';
                    return back()->with('error', "PC {$pc->name} is already booked by {$existingUser} during your selected time slot. Please choose a different PC or time.");
                }
                
                if ($pc->hourly_rate > $hourlyRate) {
                    $hourlyRate = $pc->hourly_rate;
                }
            }
            
            // Create booking for each PC in this room with the same group ID
            $pcTotal = $hourlyRate * $hours;
            
            foreach ($pcs as $pc) {
                $booking = Booking::create([
                    'tenant_id' => $tenant->id,
                    'booking_group_id' => $bookingGroupId,
                    'room_id' => $room->id,
                    'pc_id' => $pc->id,
                    'user_id' => auth()->id(),
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'hours' => $hours,
                    'total_amount' => $pcTotal,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(15),
                    'notes' => $request->notes,
                ]);
                
                // Send notification to staff
                NotificationService::bookingCreated($booking);
                
                $allBookings[] = $booking;
            }
        }
        
        if (empty($allBookings)) {
            return back()->with('error', 'No valid PCs selected for booking');
        }
        
        // Redirect to confirmation page with first booking
        return redirect()->route('website.booking.confirmation', $allBookings[0]->id)
                       ->with('success', count($allBookings) . ' booking(s) created! Waiting for approval.');
    }

    /**
     * Show booking confirmation with all group bookings
     */
    public function confirmation(Booking $booking)
    {
        // Get all bookings in this group for this user
        $groupBookings = collect([$booking]);
        if ($booking->booking_group_id) {
            $groupBookings = Booking::where('booking_group_id', $booking->booking_group_id)
                                  ->where('user_id', auth()->id())
                                  ->get();
        }

        // If no bookings found or booking doesn't belong to user, redirect
        if ($groupBookings->isEmpty() || $groupBookings->first()->user_id !== auth()->id()) {
            return redirect()->route('website.booking.my-bookings')
                           ->with('error', 'Booking not found');
        }

        // Use first booking from the group
        $booking = $groupBookings->first();

        return view('website.booking.confirmation', compact('booking', 'groupBookings'));
    }

    /**
     * Show all bookings in a group
     */
    public function groupBooking($bookingGroupId)
    {
        // Get all bookings in this group that belong to the current user
        $bookings = Booking::with(['room', 'pc', 'user'])
                          ->where('booking_group_id', $bookingGroupId)
                          ->where('user_id', auth()->id())
                          ->get();

        if ($bookings->isEmpty()) {
            abort(404);
        }

        // Get the first booking for header info
        $firstBooking = $bookings->first();

        return view('website.booking.group-booking', compact('bookings', 'firstBooking', 'bookingGroupId'));
    }

    /**
     * Display my bookings (grouped by booking_group_id)
     */
    public function myBookings()
    {
        // Get first booking from each group
        $subQuery = Booking::selectRaw('MIN(id) as min_id')
                          ->where('user_id', auth()->id())
                          ->groupBy('booking_group_id');
        
        $bookings = Booking::with(['room', 'pc', 'groupBookings.pc'])
                          ->where('user_id', auth()->id())
                          ->whereIn('id', $subQuery)
                          ->orderBy('start_time', 'desc')
                          ->paginate(20);

        return view('website.booking.my-bookings', compact('bookings'));
    }

    /**
     * Cancel my booking (all bookings in the group)
     */
    public function cancel(Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== auth()->id()) {
            return back()->with('error', 'You cannot cancel this booking');
        }

        // Get all bookings in this group
        $allBookings = $booking->getAllGroupBookings();
        
        // Check if all can be cancelled
        $canCancel = $allBookings->every(function($b) {
            return $b->canBeCancelled();
        });

        if (!$canCancel) {
            return back()->with('error', 'This booking cannot be cancelled');
        }
        
        $cancelledCount = 0;
        
        foreach ($allBookings as $groupBooking) {
            $groupBooking->cancel();
            $cancelledCount++;
        }
        
        $message = $cancelledCount > 1 
            ? "All {$cancelledCount} bookings cancelled"
            : 'Booking cancelled';

        return redirect()->route('home')
                       ->with('success', $message);
    }

    /**
     * Get available PCs for room
     */
    public function getAvailablePcs(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::find($request->room_id);
        
        // Get all available PCs in the room
        $pcs = $room->pcs()->where('status', 'available')->get();

        return response()->json([
            'pcs' => $pcs->map(function($pc) {
                return [
                    'id' => $pc->id,
                    'name' => $pc->name,
                    'hourly_rate' => $pc->hourly_rate,
                ];
            })
        ]);
    }
}
