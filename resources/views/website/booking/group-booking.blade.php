@extends('layouts.app')

@section('title', 'Booking Group ' . $bookingGroupId . ' - Gaming Zone')
@section('page_title', 'Booking Details')

@section('extra_styles')
<style>
.group-booking-page {
    padding: 20px 0;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--accent-primary);
    text-decoration: none;
    font-size: 14px;
    margin-bottom: 16px;
}

.back-link:hover {
    text-decoration: underline;
}

.group-header {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
}

.group-id {
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.group-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
}

.group-summary {
    display: flex;
    gap: 32px;
    flex-wrap: wrap;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-item i {
    color: var(--accent-primary);
    font-size: 18px;
}

.summary-item .label {
    font-size: 12px;
    color: var(--text-secondary);
}

.summary-item .value {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
}

.bookings-list {
    display: grid;
    gap: 16px;
}

.booking-card {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    transition: all 0.2s ease;
}

.booking-card:hover {
    border-color: var(--accent-primary);
}

.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.booking-pc-info h4 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.booking-pc-info p {
    font-size: 14px;
    color: var(--text-secondary);
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

.booking-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin-bottom: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item .label {
    font-size: 12px;
    color: var(--text-secondary);
}

.detail-item .value {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
}

.booking-amount {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}

.booking-actions {
    display: flex;
    gap: 12px;
    margin-top: 16px;
}

.btn-action {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
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

.total-section {
    background: var(--bg-card);
    border: 2px solid var(--accent-primary);
    border-radius: 16px;
    padding: 24px;
    margin-top: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-label {
    font-size: 16px;
    color: var(--text-secondary);
}

.total-amount {
    font-size: 32px;
    font-weight: 700;
    color: var(--accent-primary);
}

@media (max-width: 768px) {
    .group-summary {
        flex-direction: column;
        gap: 16px;
    }
    
    .booking-card-header {
        flex-direction: column;
        gap: 12px;
    }
    
    .booking-details-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .total-section {
        flex-direction: column;
        gap: 8px;
        text-align: center;
    }
}
</style>
@endsection

@section('content')
<div class="group-booking-page">
    <a href="{{ route('website.booking.my-bookings') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Back to My Bookings
    </a>

    <div class="group-header">
        <div class="group-id">Booking Group ID</div>
        <h1 class="group-title">{{ $bookingGroupId }}</h1>
        <div class="group-summary">
            <div class="summary-item">
                <i class="fas fa-desktop"></i>
                <div>
                    <div class="label">Total PCs</div>
                    <div class="value">{{ $bookings->count() }}</div>
                </div>
            </div>
            <div class="summary-item">
                <i class="fas fa-calendar"></i>
                <div>
                    <div class="label">Date</div>
                    <div class="value">{{ $firstBooking->start_time->format('M d, Y') }}</div>
                </div>
            </div>
            <div class="summary-item">
                <i class="fas fa-clock"></i>
                <div>
                    <div class="label">Time</div>
                    <div class="value">{{ $firstBooking->start_time->format('h:i A') }} - {{ $firstBooking->end_time->format('h:i A') }}</div>
                </div>
            </div>
            <div class="summary-item">
                <i class="fas fa-hourglass-half"></i>
                <div>
                    <div class="label">Duration</div>
                    <div class="value">{{ $firstBooking->hours }} hour{{ $firstBooking->hours > 1 ? 's' : '' }}</div>
                </div>
            </div>
        </div>
    </div>

    <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Booked PCs</h2>
    
    <div class="bookings-list">
        @foreach($bookings as $booking)
        <div class="booking-card">
            <div class="booking-card-header">
                <div class="booking-pc-info">
                    <h4>{{ $booking->pc->name ?? 'Unknown PC' }}</h4>
                    <p>{{ $booking->room->name ?? 'Unknown Room' }}</p>
                </div>
                @php
                    $statusConfig = [
                        'temporary' => ['icon' => 'fa-clock', 'label' => 'Pending'],
                        'pending' => ['icon' => 'fa-hourglass', 'label' => 'Pending'],
                        'confirmed' => ['icon' => 'fa-check-circle', 'label' => 'Confirmed'],
                        'completed' => ['icon' => 'fa-flag-checkered', 'label' => 'Completed'],
                        'cancelled' => ['icon' => 'fa-ban', 'label' => 'Cancelled'],
                    ];
                    $config = $statusConfig[$booking->status] ?? ['icon' => 'fa-question', 'label' => 'Unknown'];
                @endphp
                <span class="status-badge {{ $booking->status }}">
                    <i class="fas {{ $config['icon'] }}"></i>
                    {{ $config['label'] }}
                </span>
            </div>
            
            <div class="booking-details-grid">
                <div class="detail-item">
                    <span class="label">PC ID</span>
                    <span class="value">{{ $booking->pc->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <span class="label">Hourly Rate</span>
                    <span class="value">Rs. {{ number_format($booking->pc->hourly_rate ?? 0, 0) }}</span>
                </div>
                <div class="detail-item">
                    <span class="label">Start Time</span>
                    <span class="value">{{ $booking->start_time->format('h:i A') }}</span>
                </div>
                <div class="detail-item">
                    <span class="label">End Time</span>
                    <span class="value">{{ $booking->end_time->format('h:i A') }}</span>
                </div>
            </div>
            
            <div class="booking-amount">Rs. {{ number_format($booking->total_amount, 0) }}</div>
        </div>
        @endforeach
    </div>

    <div class="total-section">
        <div class="total-label">Total Amount</div>
        <div class="total-amount">Rs. {{ number_format($bookings->sum('total_amount'), 0) }}</div>
    </div>

    @php
        $canCancel = $bookings->every(function($b) {
            return $b->canBeCancelled();
        });
    @endphp

    @if($canCancel)
    <div class="booking-actions" style="margin-top: 24px; justify-content: center;">
        <a href="{{ route('website.booking.confirmation', $bookings->first()->id) }}" class="btn-action btn-view" style="background: var(--accent-primary); border-color: var(--accent-primary); color: white;">
            <i class="fas fa-receipt"></i>
            View Receipt
        </a>
        <form action="{{ route('website.booking.cancel', $bookings->first()->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel all {{ $bookings->count() }} bookings in this group?')">
            @csrf
            <button type="submit" class="btn-action btn-cancel">
                <i class="fas fa-times"></i>
                Cancel All {{ $bookings->count() }} Bookings
            </button>
        </form>
    </div>
    @else
    <div class="booking-actions" style="margin-top: 24px; justify-content: center;">
        <a href="{{ route('website.booking.confirmation', $bookings->first()->id) }}" class="btn-action btn-view" style="background: var(--accent-primary); border-color: var(--accent-primary); color: white;">
            <i class="fas fa-receipt"></i>
            View Receipt
        </a>
    </div>
    @endif
</div>
@endsection
