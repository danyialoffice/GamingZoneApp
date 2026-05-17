@extends('layouts.app')

@section('title', 'Tenant Dashboard - Gaming Zone')
@section('page_title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon primary">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Total Rooms</div>
                    <div class="stats-value">{{ $stats['total_rooms'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon success">
                    <i class="fas fa-desktop"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Total PCs</div>
                    <div class="stats-value">{{ $stats['total_pcs'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Today's Bookings</div>
                    <div class="stats-value">{{ $stats['today_bookings'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Today's Revenue</div>
                    <div class="stats-value">${{ number_format($stats['today_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 g-4">

 <div class="col-md-2">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Available PCs</div>
                    <div class="stats-value">{{ $stats['available_pcs'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Pending Bookings</div>
                    <div class="stats-value">{{ $stats['pending_bookings'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mt-4">
    <div class="col-12">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-bolt me-2" style="color: var(--accent-primary);"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body-custom">
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('tenant.rooms.create') }}" class="btn-primary-custom">
                        <i class="fas fa-plus"></i>
                        Add Room
                    </a>
                    <a href="{{ route('tenant.pcs.create') }}" class="btn-primary-custom">
                        <i class="fas fa-desktop"></i>
                        Add PC
                    </a>
                    <a href="{{ route('tenant.bookings.index') }}" class="btn-secondary-custom">
                        <i class="fas fa-list"></i>
                        View All Bookings
                    </a>
                    <a href="{{ route('website.booking.create') }}" class="btn-secondary-custom">
                        <i class="fas fa-calendar-plus"></i>
                        Create Booking
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 g-4">
   

    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-exclamation-circle me-2" style="color: var(--warning);"></i>
                    Pending Bookings
                </h5>
            </div>
            <div class="card-body-custom p-0">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>PC</th>
                            <th>Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                     <tbody>
                         @forelse($pendingBookings as $booking)
                         @php
                             $allBookings = $booking->getAllGroupBookings();
                             $pcWithRooms = $allBookings->map(function($b) {
                                 $pcName = $b->pc ? $b->pc->name : 'Unknown';
                                 $roomName = $b->room ? $b->room->name : 'Unknown';
                                 return $pcName . ' (' . $roomName . ')';
                             })->filter()->unique()->values();
                             $pcCount = $allBookings->count();
                         @endphp
                         <tr>
                             <td>{{ $booking->user->name ?? 'N/A' }}</td>
                             <td style="color: var(--text-secondary);">
                                 @if($pcCount > 1)
                                     <span title="{{ $pcWithRooms->implode('\n') }}" style="cursor: help;">
                                         {{ $pcCount }} PCs <i class="fas fa-desktop" style="font-size: 10px;"></i>
                                     </span>
                                 @else
                                     {{ $booking->pc->name ?? 'N/A' }}
                                 @endif
                             </td>
                             <td style="color: var(--text-secondary);">{{ $booking->created_at->diffForHumans() }}</td>
                             <td>
                                 <form action="{{ route('tenant.bookings.approve', $booking) }}" method="POST" style="display: inline;">
                                     @csrf
                                     <button type="submit" class="btn-primary-custom" style="padding: 6px 12px; font-size: 12px;">
                                         <i class="fas fa-check"></i>
                                     </button>
                                 </form>
                             </td>
                         </tr>
                         @empty
                         <tr>
                             <td colspan="4" class="text-center" style="padding: 32px; color: var(--text-muted);">No pending bookings</td>
                         </tr>
                         @endforelse
                     </tbody>
                </table>
            </div>
        </div>
    </div>
 


      <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-calendar-check me-2" style="color: var(--accent-primary);"></i>
                    Today's Bookings
                </h5>
            </div>
            <div class="card-body-custom p-0">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>PC</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayBookings as $booking)
                        @php
                            $allBookings = $booking->getAllGroupBookings();
                            $pcWithRooms = $allBookings->map(function($b) {
                                $pcName = $b->pc ? $b->pc->name : 'Unknown';
                                $roomName = $b->room ? $b->room->name : 'Unknown';
                                return $pcName . ' (' . $roomName . ')';
                            })->filter()->unique()->values();
                            $pcCount = $allBookings->count();
                        @endphp
                        <tr>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td style="color: var(--text-secondary);">
                                @if($pcCount > 1)
                                    <span title="{{ $pcWithRooms->implode('\n') }}" style="cursor: help;">
                                        {{ $pcCount }} PCs <i class="fas fa-desktop" style="font-size: 10px;"></i>
                                    </span>
                                @else
                                    {{ $booking->pc->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td style="color: var(--text-secondary);">{{ $booking->start_time->format('H:i') }}</td>
                            <td>
                                <span class="badge-custom badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 32px; color: var(--text-muted);">No bookings today</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
      
</div>

@endsection