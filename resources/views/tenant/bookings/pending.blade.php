@extends('layouts.app')

@section('title', 'Pending Bookings - Gaming Zone')
@section('page_title', 'Pending Bookings')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <p class="text-secondary mb-0">Bookings awaiting approval</p>
    <a href="{{ route('tenant.bookings.index') }}" class="btn-secondary-custom">
        <i class="fas fa-arrow-left"></i>
        Back to All Bookings
    </a>
</div>

<!-- Filter -->
<div class="card-custom" style="margin-bottom: 16px;">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('tenant.bookings.pending') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Group ID</label>
                        <input type="text" name="group_id" value="{{ request('group_id') }}" placeholder="Enter Group ID" class="form-control-custom">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="btn-primary-custom" style="flex: 1;">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('tenant.bookings.pending') }}" class="btn-secondary-custom">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-body-custom" style="padding: 0;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Group ID</th>
                    <th>User</th>
                    <th>PCs</th>
                    <th>Room</th>
                    <th>Date & Time</th>
                    <th>Duration</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                @php
                    // Get all bookings in this group (including this one)
                    $allBookings = $booking->getAllGroupBookings();
                    
                    // Get PC names with room
                    $pcWithRooms = $allBookings->map(function($b) {
                        $pcName = $b->pc ? $b->pc->name : 'Unknown';
                        $roomName = $b->room ? $b->room->name : 'Unknown';
                        return $pcName . ' (' . $roomName . ')';
                    })->filter()->unique()->values();
                    
                    // Get room names
                    $roomNames = $allBookings->map(function($b) {
                        return $b->room ? $b->room->name : 'Unknown';
                    })->filter()->unique()->values();
                    
                    // Calculate total amount
                    $totalAmount = $allBookings->sum('total_amount');
                    $pcCount = $allBookings->count();
                    
                    // Display ID
                    $displayId = $booking->booking_group_id ?? 'BG-' . $booking->id;
                @endphp
                <tr>
                    <td>
                        <strong>#{{ Str::limit($displayId, 15, '...') }}</strong>
                        @if($pcCount > 1)
                            <span class="badge-custom badge-info" style="font-size: 10px;">{{ $pcCount }} PCs</span>
                        @endif
                    </td>
                    <td>{{ $booking->user->name ?? 'N/A' }}</td>
                    <td style="color: var(--text-secondary);">
                        @if($pcCount > 1)
                            <span title="{{ $pcWithRooms->implode('\n') }}" style="cursor: help;">{{ Str::limit($pcWithRooms->implode(', '), 25, '...') }}</span>
                        @else
                            {{ $allBookings->first()->pc->name ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="color: var(--text-secondary);">
                        @if($roomNames->count() > 1)
                            <span title="{{ $roomNames->implode(', ') }}" style="cursor: help;">{{ $roomNames->count() }} Rooms</span>
                        @else
                            {{ $roomNames->first() ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="color: var(--text-secondary);">{{ $booking->start_time->format('M d, Y H:i') }}</td>
                    <td style="color: var(--text-secondary);">{{ $booking->hours ?? 1 }} hrs</td>
                    <td><strong>${{ number_format($totalAmount, 2) }}</strong></td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <form action="{{ route('tenant.bookings.approve', $booking) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-primary-custom" style="padding: 6px 12px; background: var(--success);" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('tenant.bookings.reject', $booking) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-primary-custom" style="padding: 6px 12px; background: var(--warning);" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 48px; color: var(--text-muted);">
                        <i class="fas fa-check-circle fa-2x mb-3" style="color: var(--success);"></i>
                        <p class="mb-0">No pending bookings! All caught up.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($bookings->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
