@extends('layouts.app')

@section('title', 'Bookings - Gaming Zone')
@section('page_title', 'Bookings')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <p class="text-secondary mb-0">Manage all bookings</p>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('tenant.settings.notifications') }}" class="btn-secondary-custom">
            <i class="fas fa-bell"></i>
            Notification Settings
        </a>
        <a href="{{ route('tenant.bookings.pending') }}" class="btn-primary-custom">
            <i class="fas fa-clock"></i>
            Pending ({{ App\Models\Booking::where('status', 'pending')->count() }})
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card-custom" style="margin-bottom: 16px;">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('tenant.bookings.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Group ID</label>
                        <input type="text" name="group_id" value="{{ request('group_id') }}" placeholder="Enter Group ID" class="form-control-custom">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control-custom">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="temporary" {{ request('status') === 'temporary' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control-custom">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control-custom">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="btn-primary-custom" style="flex: 1;">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('tenant.bookings.index') }}" class="btn-secondary-custom">
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
                    <th>Status</th>
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
                        <span class="badge-custom badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'cancelled' ? 'danger' : ($booking->status === 'completed' ? 'info' : 'secondary'))) }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('tenant.bookings.show', $booking) }}" class="btn-primary-custom" style="padding: 8px 12px;" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($booking->status === 'pending' || $booking->status === 'temporary')
                            <form action="{{ route('tenant.bookings.approve', $booking) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-primary-custom" style="padding: 8px 12px; background: var(--success);" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('tenant.bookings.reject', $booking) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-primary-custom" style="padding: 8px 12px; background: var(--warning);" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                            
                            @if($booking->canBeCancelled())
                            <form action="{{ route('tenant.bookings.cancel', $booking) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-primary-custom" style="padding: 8px 12px; background: var(--danger);" title="Cancel">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            @endif
                            
                            <form action="{{ route('tenant.bookings.destroy', $booking) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this booking? The user will be notified.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-primary-custom" style="padding: 8px 12px; background: #1f2937;" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 48px; color: var(--text-muted);">
                        <i class="fas fa-calendar-times fa-2x mb-3"></i>
                        <p class="mb-0">No bookings found</p>
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
