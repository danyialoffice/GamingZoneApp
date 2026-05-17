@extends('layouts.app')

@section('title', 'Notifications - Gaming Zone')
@section('page_title', 'Notifications')

@section('extra_styles')
<style>
.notifications-page {
    padding: 20px 0;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.notifications-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.notifications-title h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.unread-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    background: var(--accent-primary);
    color: white;
    font-size: 12px;
    font-weight: 600;
    border-radius: 12px;
}

.notifications-actions {
    display: flex;
    gap: 12px;
}

.btn-mark-all-read {
    padding: 10px 20px;
    background: transparent;
    color: var(--accent-primary);
    border: 2px solid var(--accent-primary);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-mark-all-read:hover {
    background: var(--accent-primary);
    color: white;
}

.btn-delete-read {
    padding: 10px 20px;
    background: transparent;
    color: var(--danger);
    border: 2px solid var(--danger);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-delete-read:hover {
    background: var(--danger);
    color: white;
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 10px 16px;
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-tab:hover {
    border-color: var(--accent-primary);
    color: var(--accent-primary);
}

.filter-tab.active {
    background: var(--accent-primary);
    border-color: var(--accent-primary);
    color: white;
}

.filter-tab .count {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
}

.filter-tab:not(.active) .count {
    background: var(--bg-secondary);
}

/* Notifications List */
.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notification-item {
    display: flex;
    gap: 16px;
    padding: 16px 20px;
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.notification-item:hover {
    border-color: var(--accent-primary);
    transform: translateX(4px);
}

.notification-item.unread {
    border-left: 4px solid var(--accent-primary);
    background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, var(--bg-card) 100%);
}

.notification-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}

.notification-icon.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.notification-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.notification-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.notification-icon.info { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
.notification-icon.primary { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
.notification-icon.secondary { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 4px;
}

.notification-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
}

.notification-time {
    font-size: 12px;
    color: var(--text-muted);
    white-space: nowrap;
}

.notification-message {
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 8px;
}

.notification-meta {
    display: flex;
    gap: 12px;
    align-items: center;
}

.notification-type {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.notification-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.action-btn {
    padding: 6px 12px;
    background: transparent;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    color: var(--text-secondary);
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.action-btn:hover {
    background: var(--bg-secondary);
    color: var(--text-primary);
}

.action-btn.read:hover {
    border-color: var(--accent-primary);
    color: var(--accent-primary);
}

.action-btn.delete:hover {
    border-color: var(--danger);
    color: var(--danger);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--bg-card);
    border: 2px dashed var(--border-color);
    border-radius: 16px;
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
    font-size: 14px;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .notifications-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .notifications-actions {
        width: 100%;
        flex-wrap: wrap;
    }
    
    .btn-mark-all-read,
    .btn-delete-read {
        flex: 1;
        justify-content: center;
    }
    
    .filter-tabs {
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 8px;
        -webkit-overflow-scrolling: touch;
    }
    
    .filter-tab {
        white-space: nowrap;
    }
    
    .notification-item {
        flex-direction: column;
        gap: 12px;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .notification-actions {
        opacity: 1;
        flex-wrap: wrap;
    }
}
</style>
@endsection

@section('content')
<div class="notifications-page">
    <!-- Header -->
    <div class="notifications-header">
        <div class="notifications-title">
            <h2>Notifications</h2>
            @if($unreadCount > 0)
                <span class="unread-badge">{{ $unreadCount }} unread</span>
            @endif
        </div>
        <div class="notifications-actions">
            @if($unreadCount > 0)
                <button class="btn-mark-all-read" onclick="markAllAsRead()">
                    <i class="fas fa-check-double"></i>
                    Mark All as Read
                </button>
            @endif
            <button class="btn-delete-read" onclick="deleteAllRead()">
                <i class="fas fa-trash"></i>
                Delete Read
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="{{ route('notifications.index', ['type' => 'all']) }}" class="filter-tab {{ $filterType === 'all' ? 'active' : '' }}">
            All <span class="count">{{ $typeStats['all'] }}</span>
        </a>
        <a href="{{ route('notifications.index', ['type' => 'unread']) }}" class="filter-tab {{ $filterType === 'unread' ? 'active' : '' }}">
            Unread <span class="count">{{ $typeStats['unread'] }}</span>
        </a>
        <a href="{{ route('notifications.index', ['type' => 'read']) }}" class="filter-tab {{ $filterType === 'read' ? 'active' : '' }}">
            Read <span class="count">{{ $typeStats['read'] }}</span>
        </a>
        <a href="{{ route('notifications.index', ['type' => 'booking_created']) }}" class="filter-tab {{ $filterType === 'booking_created' ? 'active' : '' }}">
            Bookings <span class="count">{{ $typeStats['booking_created'] }}</span>
        </a>
        <a href="{{ route('notifications.index', ['type' => 'booking_approved']) }}" class="filter-tab {{ $filterType === 'booking_approved' ? 'active' : '' }}">
            Approved <span class="count">{{ $typeStats['booking_approved'] }}</span>
        </a>
        <a href="{{ route('notifications.index', ['type' => 'booking_rejected']) }}" class="filter-tab {{ $filterType === 'booking_rejected' ? 'active' : '' }}">
            Rejected <span class="count">{{ $typeStats['booking_rejected'] }}</span>
        </a>
        <a href="{{ route('notifications.index', ['type' => 'payment_received']) }}" class="filter-tab {{ $filterType === 'payment_received' ? 'active' : '' }}">
            Payments <span class="count">{{ $typeStats['payment_received'] }}</span>
        </a>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        @forelse($notifications as $notification)
            <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" 
                 data-id="{{ $notification->id }}"
                 onclick="handleNotificationClick({{ $notification->id }}, '{{ $notification->type }}', {{ json_encode($notification->data) }})">
                <div class="notification-icon {{ $notification->getColor() }}">
                    <i class="fas {{ $notification->getIcon() }}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <span class="notification-title">{{ $notification->title }}</span>
                        <span class="notification-time">{{ $notification->getTimeAgo() }}</span>
                    </div>
                    <p class="notification-message">{{ $notification->message }}</p>
                    <div class="notification-meta">
                        <span class="notification-type">{{ str_replace('_', ' ', $notification->type) }}</span>
                        <div class="notification-actions">
                            @if(!$notification->is_read)
                                <button class="action-btn read" onclick="event.stopPropagation(); markAsRead({{ $notification->id }})">
                                    <i class="fas fa-check"></i>
                                    Mark Read
                                </button>
                            @endif
                            <button class="action-btn delete" onclick="event.stopPropagation(); deleteNotification({{ $notification->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications</h3>
                <p>You don't have any notifications yet. We'll notify you when something important happens!</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="pagination-wrapper">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// Mark single notification as read
function markAsRead(id) {
    fetch(`/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (item) {
                item.classList.remove('unread');
                // Hide the mark read button
                const readBtn = item.querySelector('.action-btn.read');
                if (readBtn) readBtn.remove();
            }
            updateUnreadBadge(data.unread_count);
        }
    });
}

// Mark all as read
function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;
    
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            document.querySelectorAll('.action-btn.read').forEach(btn => btn.remove());
            updateUnreadBadge(0);
            // Refresh page to update counts
            location.reload();
        }
    });
}

// Delete single notification
function deleteNotification(id) {
    if (!confirm('Delete this notification?')) return;
    
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (item) {
                item.remove();
            }
            updateUnreadBadge(data.unread_count);
        }
    });
}

// Delete all read notifications
function deleteAllRead() {
    if (!confirm('Delete all read notifications?')) return;
    
    fetch('/notifications/delete-all-read', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Handle notification click - redirect based on type
function handleNotificationClick(id, type, data) {
    // Mark as read first
    if (data && !data.is_read) {
        markAsRead(id);
    }
    
    // Redirect based on notification type
    if (data && data.booking_id) {
        window.location.href = `/booking/${data.booking_id}`;
    } else if (type === 'booking_created' || type === 'booking_approved' || 
               type === 'booking_rejected' || type === 'booking_cancelled') {
        window.location.href = '/booking/my-bookings';
    } else if (type.startsWith('payment')) {
        window.location.href = '/booking/my-bookings';
    }
}

// Update unread badge in header
function updateUnreadBadge(count) {
    const badges = document.querySelectorAll('.notification-badge');
    badges.forEach(badge => {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    });
}
</script>
@endsection
