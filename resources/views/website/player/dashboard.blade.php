@extends('layouts.app')

@section('title', 'My Dashboard - Gaming Zone')

@section('page_title', 'My Dashboard')

@section('extra_styles')
<style>
.player-dashboard {
    padding: 20px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: var(--bg-card);
    border-radius: 16px;
    padding: 24px;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--accent-primary);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    font-size: 24px;
}

.stat-icon.hours { background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); }
.stat-icon.bookings { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-icon.payment { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stat-icon.upcoming { background: rgba(236, 72, 153, 0.1); color: #ec4899; }

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
}

.stat-sublabel {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 8px;
}

/* Month Stats */
.month-stats {
    background: var(--bg-card);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    border: 2px solid var(--border-color);
}

.month-stats-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.month-stats-header i {
    font-size: 20px;
    color: var(--accent-primary);
}

.month-stats-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.month-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.month-stat {
    text-align: center;
    padding: 16px;
    background: var(--bg-secondary);
    border-radius: 12px;
}

.month-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--accent-primary);
}

.month-stat-label {
    font-size: 12px;
    color: var(--text-secondary);
    margin-top: 4px;
}

/* Sections */
.dashboard-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 992px) {
    .dashboard-sections {
        grid-template-columns: 1fr;
    }
}

.section-card {
    background: var(--bg-card);
    border-radius: 16px;
    border: 2px solid var(--border-color);
    overflow: hidden;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: var(--bg-secondary);
    border-bottom: 2px solid var(--border-color);
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: var(--accent-primary);
}

.section-link {
    font-size: 13px;
    color: var(--accent-primary);
    text-decoration: none;
    font-weight: 500;
}

.section-link:hover {
    text-decoration: underline;
}

.section-body {
    padding: 16px 20px;
}

/* Booking List */
.booking-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--bg-secondary);
    border-radius: 10px;
    margin-bottom: 10px;
}

.booking-item:last-child {
    margin-bottom: 0;
}

.booking-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.booking-icon.confirmed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.booking-icon.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.booking-icon.completed { background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); }

.booking-info {
    flex: 1;
}

.booking-date {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
}

.booking-meta {
    font-size: 12px;
    color: var(--text-secondary);
}

.booking-time {
    font-size: 12px;
    color: var(--text-muted);
}

.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.confirmed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.status-badge.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.status-badge.completed { background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); }

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    text-decoration: none;
    color: var(--text-primary);
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.action-btn:hover {
    border-color: var(--accent-primary);
    background: rgba(99, 102, 241, 0.05);
}

.action-btn i {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.action-btn.book i { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.action-btn.status i { background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); }
.action-btn.history i { background: rgba(236, 72, 153, 0.1); color: #ec4899; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 32px 16px;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 40px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state p {
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .month-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-value {
        font-size: 24px;
    }
}
</style>
@endsection

@section('content')
<div class="player-dashboard">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon hours">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ $totalHours }}</div>
            <div class="stat-label">Total Hours Played</div>
            <div class="stat-sublabel">Across all sessions</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bookings">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-value">{{ $totalBookingGroups }}</div>
            <div class="stat-label">Booking Groups</div>
            <div class="stat-sublabel">Total reservations</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon payment">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-value">${{ number_format($totalPaid, 2) }}</div>
            <div class="stat-label">Total Spent</div>
            <div class="stat-sublabel">All payments completed</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon upcoming">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value">{{ $upcomingBookings->count() }}</div>
            <div class="stat-label">Upcoming Bookings</div>
            <div class="stat-sublabel">Waiting for approval</div>
        </div>
    </div>

    <!-- This Month Stats -->
    <div class="month-stats">
        <div class="month-stats-header">
            <i class="fas fa-calendar-alt"></i>
            <h3>This Month</h3>
        </div>
        <div class="month-grid">
            <div class="month-stat">
                <div class="month-stat-value">{{ $thisMonthHours }}</div>
                <div class="month-stat-label">Hours Played</div>
            </div>
            <div class="month-stat">
                <div class="month-stat-value">${{ number_format($thisMonthSpent, 2) }}</div>
                <div class="month-stat-label">Amount Spent</div>
            </div>
            <div class="month-stat">
                <div class="month-stat-value">{{ $thisMonthBookings }}</div>
                <div class="month-stat-label">Bookings Made</div>
            </div>
        </div>
    </div>

    <!-- Dashboard Sections -->
    <div class="dashboard-sections">
        <!-- Upcoming Bookings -->
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Upcoming Bookings
                </span>
                <a href="{{ route('website.booking.my-bookings') }}" class="section-link">View All</a>
            </div>
            <div class="section-body">
                @if($upcomingBookings->count() > 0)
                    @foreach($upcomingBookings as $booking)
                    <div class="booking-item">
                        <div class="booking-icon {{ $booking->status }}">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div class="booking-info">
                            <div class="booking-date">{{ $booking->start_time->format('M d, Y') }}</div>
                            <div class="booking-meta">
                                {{ $booking->room->name ?? 'Room' }} • {{ $booking->hours }} hr{{ $booking->hours > 1 ? 's' : '' }}
                            </div>
                            <div class="booking-time">{{ $booking->start_time->format('h:i A') }}</div>
                        </div>
                        <span class="status-badge {{ $booking->status }}">
                            {{ $booking->status }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No upcoming bookings</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </span>
            </div>
            <div class="section-body">
                <div class="quick-actions">
                    <a href="{{ route('website.booking.create') }}" class="action-btn book">
                        <i class="fas fa-plus"></i>
                        Book New PC
                    </a>
                    <a href="{{ route('website.booking.pc-status') }}" class="action-btn status">
                        <i class="fas fa-tv"></i>
                        View PC Status
                    </a>
                    <a href="{{ route('website.booking.my-bookings') }}" class="action-btn history">
                        <i class="fas fa-history"></i>
                        Booking History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection