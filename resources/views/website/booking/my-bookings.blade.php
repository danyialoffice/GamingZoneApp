@extends('layouts.app')

@section('title', 'My Bookings - Gaming Zone')
@section('page_title', 'My Bookings')

@section('extra_styles')
<style>
.bookings-page {
    padding: 20px 0;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
}

.bookings-grid {
    display: grid;
    gap: 16px;
}

.booking-card {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 16px;
    align-items: center;
    transition: all 0.2s ease;
}

.booking-card:hover {
    border-color: var(--accent-primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.booking-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.booking-room-pc {
    display: flex;
    align-items: center;
    gap: 12px;
}

.booking-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.booking-icon.booked { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.booking-icon.temporary { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.booking-icon.available { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.booking-icon.pending { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
.booking-icon.completed { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

.booking-details h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.booking-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 13px;
    color: var(--text-secondary);
}

.booking-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.booking-meta-item i {
    color: var(--accent-primary);
}

.booking-status {
    text-align: right;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.temporary {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid #f59e0b;
}

.status-badge.pending {
    background: rgba(99, 102, 241, 0.1);
    color: #6366f1;
    border: 1px solid #6366f1;
}

.status-badge.confirmed {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid #10b981;
}

.status-badge.completed {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
    border: 1px solid #6b7280;
}

.status-badge.cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid #ef4444;
}

.status-badge.expired {
    background: rgba(107, 114, 128, 0.1);
    color: #9ca3af;
    border: 1px solid #9ca3af;
}

.booking-amount {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin-top: 8px;
}

.booking-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.btn-action {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-cancel {
    background: transparent;
    border: 2px solid #ef4444;
    color: #ef4444;
}

.btn-cancel:hover {
    background: #ef4444;
    color: white;
}

.btn-view {
    background: var(--accent-primary);
    border: 2px solid var(--accent-primary);
    color: white;
}

.btn-view:hover {
    background: var(--accent-secondary);
    border-color: var(--accent-secondary);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--bg-card);
    border: 2px dashed var(--border-color);
    border-radius: 12px;
}

.empty-state i {
    font-size: 64px;
    color: var(--text-muted);
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 20px;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.empty-state p {
    color: var(--text-secondary);
    margin-bottom: 24px;
}

.btn-primary {
    padding: 12px 24px;
    background: var(--accent-primary);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: var(--accent-secondary);
    transform: translateY(-2px);
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 24px;
}

.pagination {
    display: flex;
    gap: 8px;
}

.pagination a, .pagination span {
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination a {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.pagination a:hover {
    border-color: var(--accent-primary);
    color: var(--accent-primary);
}

.pagination span.current {
    background: var(--accent-primary);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .booking-card {
        grid-template-columns: 1fr;
    }
    
    .booking-status {
        text-align: left;
    }
    
    .booking-meta {
        flex-direction: column;
        gap: 8px;
    }
}
</style>
@endsection

@section('content')
<div class="bookings-page">
    <div class="page-header">
        <h1 class="page-title">My Bookings</h1>
    </div>

    @if($bookings->isEmpty())
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>No Bookings Yet</h3>
        <p>You haven't made any bookings yet. Start by checking available PCs!</p>
        <a href="{{ route('website.booking.pc-status') }}" class="btn-primary">
            <i class="fas fa-desktop"></i>
            View PC Status
        </a>
    </div>
    @else
    <div class="bookings-grid">
        @foreach($bookings as $booking)
        @php
            // Get all PCs in this booking group
            $groupBookings = $booking->groupBookings;
            $allBookings = $groupBookings->merge(collect([$booking]));
            $allBookings = $allBookings->filter()->unique('id');
            
            // Get PC names
            $pcNames = $allBookings->map(function($b) {
                return $b->pc ? $b->pc->name : 'Unknown';
            })->filter()->unique()->values();
            
            // Calculate total amount
            $totalAmount = $allBookings->sum('total_amount');
            $pcCount = $allBookings->count();
        @endphp
        <div class="booking-card">
            <div class="booking-info">
                <div class="booking-room-pc">
                    <div class="booking-icon {{ $booking->status }}">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="booking-details">
                        <h4>
                            @if($pcCount > 1)
                                {{ $pcCount }} PCs - {{ $booking->room->name ?? 'Unknown Room' }}
                                <small style="color: var(--text-secondary); display: block; font-size: 12px;">{{ $pcNames->implode(', ') }}</small>
                            @else
                                {{ $pcNames->first() ?? 'Unknown PC' }} - {{ $booking->room->name ?? 'Unknown Room' }}
                            @endif
                        </h4>
                        <div class="booking-meta">
                            <span class="booking-meta-item">
                                <i class="fas fa-calendar"></i>
                                {{ $booking->start_time->format('M d, Y') }}
                            </span>
                            <span class="booking-meta-item">
                                <i class="fas fa-clock"></i>
                                {{ $booking->start_time->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}
                            </span>
                            <span class="booking-meta-item">
                                <i class="fas fa-hourglass-half"></i>
                                {{ $booking->hours }} hour{{ $booking->hours > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="booking-actions">
                            @if($booking->canBeCancelled())
                            <form action="{{ route('website.booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking{{ $pcCount > 1 ? ' and all ' . $pcCount . ' related bookings' : '' }}?')">
                                @csrf
                                <button type="submit" class="btn-action btn-cancel">
                                    <i class="fas fa-times"></i>
                                    Cancel {{ $pcCount > 1 ? 'All ' . $pcCount . ' Bookings' : 'Booking' }}
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('website.booking.group', $booking->booking_group_id) }}" class="btn-action btn-view">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="booking-status">
                @php
                    $statusConfig = [
                        'temporary' => ['icon' => 'fa-clock', 'label' => 'Pending Approval'],
                        'pending' => ['icon' => 'fa-hourglass', 'label' => 'Pending Approval'],
                        'confirmed' => ['icon' => 'fa-check-circle', 'label' => 'Confirmed'],
                        'completed' => ['icon' => 'fa-flag-checkered', 'label' => 'Completed'],
                        'cancelled' => ['icon' => 'fa-ban', 'label' => 'Cancelled'],
                        'expired' => ['icon' => 'fa-clock', 'label' => 'Expired'],
                    ];
                    $config = $statusConfig[$booking->status] ?? ['icon' => 'fa-question', 'label' => 'Unknown'];
                @endphp
                <span class="status-badge {{ $booking->status }}">
                    <i class="fas {{ $config['icon'] }}"></i>
                    {{ $config['label'] }}
                </span>
                <div class="booking-amount">Rs. {{ number_format($totalAmount, 0) }}</div>
            </div>
        </div>
        @endforeach
    </div>

    @if($bookings->hasPages())
    <div class="pagination-wrapper">
        {{ $bookings->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
