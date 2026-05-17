<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PC;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display list of bookings (grouped by booking_group_id)
     */
    public function index(Request $request)
    {
        $tenant = Tenant::current();
        
        // Get all booking_group_ids (including NULL for standalone)
        $subQuery = Booking::selectRaw('MIN(id) as min_id')
                          ->where('tenant_id', $tenant->id)
                          ->groupBy('booking_group_id');
        
        // Get only the first booking from each group, or standalone bookings
        $query = Booking::with(['user', 'pc', 'room', 'groupBookings.pc'])
                        ->where('tenant_id', $tenant->id)
                        ->whereIn('id', $subQuery);

        // Filter by Group ID
        if ($request->has('group_id') && $request->group_id) {
            $searchId = str_replace('#', '', $request->group_id);
            $query->where('booking_group_id', 'LIKE', '%' . $searchId . '%');
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ( $request->date_from) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('end_time', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Order by start_time desc
        $query->orderBy('start_time', 'desc');
        
        // Get bookings
        $bookings = $query->paginate(20);

        return view('tenant.bookings.index', compact('bookings'));
    }

    /**
     * Display pending bookings for approval
     */
    public function pending(Request $request)
    {
        $tenant = Tenant::current();
        
        // Get first booking from each group that has pending status
        $subQuery = Booking::selectRaw('MIN(id) as min_id')
                          ->where('tenant_id', $tenant->id)
                          ->where('status', 'pending')
                          ->groupBy('booking_group_id');
        
        $query = Booking::with(['user', 'pc', 'room', 'groupBookings.pc'])
                          ->where('tenant_id', $tenant->id)
                          ->whereIn('id', $subQuery);

        // Filter by Group ID
        if ($request->has('group_id') && $request->group_id) {
            $searchId = str_replace('#', '', $request->group_id);
            $query->where('booking_group_id', 'LIKE', '%' . $searchId . '%');
        }

        // Order by created_at asc
        $query->orderBy('created_at', 'asc');

        $bookings = $query->paginate(20);

        return view('tenant.bookings.pending', compact('bookings'));
    }

    /**
     * Show booking details
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'pc', 'room', 'approver', 'groupBookings.pc']);

        return view('tenant.bookings.show', compact('booking'));
    }

    /**
     * Approve booking (all PCs in the group)
     */
    public function approve(Request $request, Booking $booking)
    {
        // Get all bookings in this group
        $allBookings = $booking->getAllGroupBookings();
        
        if ($allBookings->isEmpty()) {
            return back()->with('error', 'Booking not found');
        }
        
        // Check if all can be approved
        $canApprove = $allBookings->every(function($b) {
            return $b->canBeApproved();
        });
        
        if (!$canApprove) {
            return back()->with('error', 'One or more bookings in this group cannot be approved');
        }
        
        $approvedCount = 0;
        $pcNames = [];
        
        foreach ($allBookings as $groupBooking) {
            $groupBooking->approve(auth()->user());
            NotificationService::bookingApproved($groupBooking, auth()->id());
            $pcNames[] = $groupBooking->pc->name ?? 'Unknown PC';
            $approvedCount++;
        }
        
        $message = $approvedCount > 1 
            ? "All {$approvedCount} bookings ({$booking->user->name}'s booking for " . implode(', ', $pcNames) . ") approved successfully"
            : 'Booking approved successfully';
        
        return redirect()->route('tenant.bookings.show', $booking->id)
                       ->with('success', $message);
    }

    /**
     * Reject booking (all PCs in the group)
     */
    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        // Get all bookings in this group
        $allBookings = $booking->getAllGroupBookings();
        
        if ($allBookings->isEmpty()) {
            return back()->with('error', 'Booking not found');
        }
        
        // Check if all can be rejected
        $canReject = $allBookings->every(function($b) {
            return $b->canBeApproved();
        });
        
        if (!$canReject) {
            return back()->with('error', 'One or more bookings in this group cannot be rejected');
        }
        
        $rejectedCount = 0;
        
        foreach ($allBookings as $groupBooking) {
            $groupBooking->reject(auth()->user(), $request->reason);
            NotificationService::bookingRejected($groupBooking, $request->reason, auth()->id());
            $rejectedCount++;
        }
        
        $message = $rejectedCount > 1 
            ? "All {$rejectedCount} bookings rejected"
            : 'Booking rejected';
        
        return redirect()->route('tenant.bookings.index')
                       ->with('success', $message);
    }

    /**
     * Cancel booking (all PCs in the group)
     */
    public function cancel(Request $request, Booking $booking)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        // Get all bookings in this group
        $allBookings = $booking->getAllGroupBookings();
        
        if ($allBookings->isEmpty()) {
            return back()->with('error', 'Booking not found');
        }
        
        // Check if all can be cancelled
        $canCancel = $allBookings->every(function($b) {
            return $b->canBeCancelled();
        });
        
        if (!$canCancel) {
            return back()->with('error', 'One or more bookings in this group cannot be cancelled');
        }
        
        $cancelledCount = 0;
        
        foreach ($allBookings as $groupBooking) {
            $groupBooking->cancel($request->reason);
            NotificationService::bookingCancelled($groupBooking, auth()->id());
            $cancelledCount++;
        }
        
        $message = $cancelledCount > 1 
            ? "All {$cancelledCount} bookings cancelled"
            : 'Booking cancelled';
        
        return redirect()->route('tenant.bookings.index')
                       ->with('success', $message);
    }

    /**
     * Check in (all PCs in the group)
     */
    public function checkIn(Booking $booking)
    {
        // Get all bookings in this group
        $allBookings = $booking->getAllGroupBookings();
        
        if ($allBookings->isEmpty()) {
            return back()->with('error', 'Booking not found');
        }
        
        // Check if all can be checked in
        $canCheckIn = $allBookings->every(function($b) {
            return $b->canCheckIn();
        });
        
        if (!$canCheckIn) {
            return back()->with('error', 'One or more bookings in this group cannot be checked in');
        }
        
        $checkedInCount = 0;
        
        foreach ($allBookings as $groupBooking) {
            $groupBooking->checkIn();
            $checkedInCount++;
        }
        
        $message = $checkedInCount > 1 
            ? "All {$checkedInCount} bookings checked in successfully"
            : 'Booking checked in successfully';
        
        return redirect()->route('tenant.bookings.show', $booking->id)
                       ->with('success', $message);
    }

    /**
     * Check out (all PCs in the group)
     */
    public function checkOut(Booking $booking)
    {
        // Get all bookings in this group
        $allBookings = $booking->getAllGroupBookings();
        
        if ($allBookings->isEmpty()) {
            return back()->with('error', 'Booking not found');
        }
        
        // Check if all can be checked out
        $canCheckOut = $allBookings->every(function($b) {
            return $b->canCheckOut();
        });
        
        if (!$canCheckOut) {
            return back()->with('error', 'One or more bookings in this group cannot be checked out');
        }
        
        $checkedOutCount = 0;
        
        foreach ($allBookings as $groupBooking) {
            $groupBooking->checkOut();
            $checkedOutCount++;
        }
        
        $message = $checkedOutCount > 1 
            ? "All {$checkedOutCount} bookings checked out successfully"
            : 'Booking checked out successfully';
        
        return redirect()->route('tenant.bookings.show', $booking->id)
                       ->with('success', $message);
    }

    /**
     * Delete booking (all PCs in the group)
     */
    public function destroy(Request $request, Booking $booking)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);
        
        // Get all bookings in this group
        $allBookings = $booking->getAllGroupBookings();
        
        if ($allBookings->isEmpty()) {
            return back()->with('error', 'Booking not found');
        }
        
        $deletedCount = 0;
        $pcNames = [];
        $userId = $booking->user_id;
        $tenantId = $booking->tenant_id;
        
        foreach ($allBookings as $groupBooking) {
            $pcNames[] = $groupBooking->pc->name ?? 'Unknown PC';
            $groupBooking->delete();
            $deletedCount++;
        }
        
        // Send one notification for the entire group
        $pcList = implode(', ', array_unique($pcNames));
        $message = $deletedCount > 1 
            ? "All {$deletedCount} bookings ({$pcList}) deleted. User has been notified."
            : "Booking for {$pcList} deleted. User has been notified.";
        
        NotificationService::create(
            $userId,
            $tenantId,
            NotificationService::TYPE_SYSTEM,
            'Booking(s) Deleted',
            "Your booking(s) for {$pcList} have been deleted by an administrator." . 
            ($request->reason ? " Reason: {$request->reason}" : ''),
            [
                'booking_group_id' => $booking->booking_group_id,
                'deleted_by' => auth()->id(),
                'reason' => $request->reason,
                'deleted_count' => $deletedCount,
            ]
        );

        return redirect()->route('tenant.bookings.index')
                       ->with('success', $message);
    }
}
