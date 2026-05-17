@extends('layouts.app')

@section('title', 'Booking Details - Gaming Zone')
@section('page_title', 'Booking #' . $booking->id)

@section('content')
@php
    // Get all bookings in this group
    $allBookings = $booking->getAllGroupBookings();
    $pcCount = $allBookings->count();
    $totalAmount = $allBookings->sum('total_amount');
    $groupId = $booking->booking_group_id ?? 'BG-' . $booking->id;
@endphp

<div class="breadcrumb-custom">
    <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('tenant.bookings.index') }}">Bookings</a>
    <i class="fas fa-chevron-right"></i>
    <span>#{{ Str::limit($groupId, 15, '...') }}</span>
</div>

@if($pcCount > 1)
<div class="alert alert-info" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #3b82f6; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
    <i class="fas fa-layer-group me-2"></i>
    <strong>Group Booking:</strong> This booking contains {{ $pcCount }} PCs ({{ $allBookings->map(fn($b) => $b->pc->name ?? 'Unknown')->implode(', ') }})
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-calendar-check me-2" style="color: var(--accent-primary);"></i>
                    Booking Details
                </h5>
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">GROUP ID</div>
                            <div style="font-weight: 500;"><strong>#{{ $groupId }}</strong></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">USER</div>
                            <div style="font-weight: 500;">{{ $booking->user->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">ROOM</div>
                            <div style="font-weight: 500;">{{ $booking->room->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">PC(s)</div>
                            @if($pcCount > 1)
                                <div style="font-weight: 500;">
                                    @foreach($allBookings as $gb)
                                        <span class="badge-custom badge-info" style="margin-right: 4px; margin-bottom: 4px;">{{ $gb->pc->name ?? 'Unknown' }}</span>
                                    @endforeach
                                </div>
                            @else
                                <div style="font-weight: 500;">{{ $booking->pc->name ?? 'N/A' }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">START TIME</div>
                            <div style="font-weight: 500;">{{ $booking->start_time->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">DURATION</div>
                            <div style="font-weight: 500;">{{ $booking->hours }} hour{{ $booking->hours > 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">END TIME</div>
                            <div style="font-weight: 500;">{{ $booking->end_time->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">TOTAL AMOUNT</div>
                            <div style="font-weight: 500; font-size: 20px;">${{ number_format($totalAmount, 2) }}</div>
                            @if($pcCount > 1)
                                <small style="color: var(--text-secondary);">for {{ $pcCount }} PCs</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div style="margin-bottom: 20px;">
                            <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">STATUS</div>
                            <span class="badge-custom badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-cog me-2" style="color: var(--accent-primary);"></i>
                    Actions
                </h5>
            </div>
            <div class="card-body-custom">
                @php
                    $canApproveAll = $allBookings->every(fn($b) => $b->canBeApproved());
                    $canCancelAll = $allBookings->every(fn($b) => $b->canBeCancelled());
                    $canCheckInAll = $allBookings->every(fn($b) => $b->canCheckIn());
                    $canCheckOutAll = $allBookings->every(fn($b) => $b->canCheckOut());
                @endphp
                
                @if($booking->status === 'pending' || $booking->status === 'temporary')
                    @if($canApproveAll)
                        <form action="{{ route('tenant.bookings.approve', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary-custom" style="width: 100%; margin-bottom: 12px;">
                                <i class="fas fa-check"></i>
                                Approve {{ $pcCount > 1 ? 'All ' . $pcCount . ' Bookings' : 'Booking' }}
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('tenant.bookings.reject', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary-custom" style="width: 100%; margin-bottom: 12px;">
                            <i class="fas fa-times"></i>
                            Reject {{ $pcCount > 1 ? 'All ' . $pcCount . ' Bookings' : 'Booking' }}
                        </button>
                    </form>
                @endif
                
                @if($booking->status === 'confirmed')
                    @if($canCheckInAll)
                        <form action="{{ route('tenant.bookings.check-in', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary-custom" style="width: 100%; margin-bottom: 12px;">
                                <i class="fas fa-sign-in-alt"></i>
                                Check In {{ $pcCount > 1 ? 'All' : '' }}
                            </button>
                        </form>
                    @endif
                @endif
                
                @if($booking->status === 'checked_in')
                    @if($canCheckOutAll)
                        <form action="{{ route('tenant.bookings.check-out', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary-custom" style="width: 100%; margin-bottom: 12px;">
                                <i class="fas fa-sign-out-alt"></i>
                                Check Out {{ $pcCount > 1 ? 'All' : '' }}
                            </button>
                        </form>
                    @endif
                @endif
                
                @if($canCancelAll)
                    <form action="{{ route('tenant.bookings.cancel', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary-custom" style="width: 100%; color: var(--danger);">
                            <i class="fas fa-ban"></i>
                            Cancel {{ $pcCount > 1 ? 'All ' . $pcCount . ' Bookings' : 'Booking' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
        
        <div class="card-custom" style="margin-top: 16px;">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-trash me-2" style="color: var(--danger);"></i>
                    Danger Zone
                </h5>
            </div>
            <div class="card-body-custom">
                <form action="{{ route('tenant.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this booking{{ $pcCount > 1 ? ' and all ' . $pcCount . ' related bookings' : '' }}? The user will be notified.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-primary-custom" style="width: 100%; background: var(--danger);">
                        <i class="fas fa-trash"></i>
                        Delete {{ $pcCount > 1 ? 'All ' . $pcCount . ' Bookings' : 'Booking' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
