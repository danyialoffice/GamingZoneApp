@extends('layouts.app')

@section('title', 'PC Status - Gaming Zone')
@section('page_title', 'PC Status Dashboard')

@section('extra_styles')
<style>
.pc-dashboard {
    padding: 20px 0;
}

/* Top Bar with Toggle */
.status-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding: 16px 24px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    flex-wrap: wrap;
    gap: 16px;
}

.topbar-left, .topbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.topbar-right {
    gap: 24px;
}

.status-toggle {
    display: flex;
    align-items: center;
    gap: 12px;
}

.status-toggle-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-secondary);
}

/* View Toggle Buttons */
.view-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
}

.view-toggle-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
}

.view-buttons {
    display: flex;
    gap: 4px;
    background: var(--bg-secondary);
    padding: 4px;
    border-radius: 8px;
}

.view-btn {
    padding: 8px 12px;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.view-btn:hover {
    background: var(--bg-primary);
    color: var(--text-primary);
}

.view-btn.active {
    background: var(--accent-primary);
    color: white;
}

.toggle-switch {
    position: relative;
    width: 52px;
    height: 26px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--border-color);
    border-radius: 26px;
    transition: 0.3s;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: 0.3s;
}

.toggle-switch input:checked + .toggle-slider {
    background: #10b981;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

/* Legend */
.legend-badges {
    display: flex;
    gap: 16px;
}

.legend-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-secondary);
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.legend-dot.available { background: #10b981; }
.legend-dot.booked { background: #ef4444; }
.legend-dot.temporary { background: #f59e0b; }

/* Selection Bar */
.selection-bar {
    display: none;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding: 16px 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 12px;
    color: white;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.selection-bar.show {
    display: flex;
}

.selection-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.selection-count {
    font-size: 18px;
    font-weight: 700;
}

.selection-actions {
    display: flex;
    gap: 12px;
}

.btn-select-action {
    padding: 10px 20px;
    border: 2px solid white;
    border-radius: 8px;
    background: transparent;
    color: white;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-select-action:hover {
    background: white;
    color: #10b981;
}

.btn-book-selected {
    padding: 10px 24px;
    border: none;
    border-radius: 8px;
    background: white;
    color: #10b981;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-book-selected:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Room Container */
.room-container {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    margin-bottom: 24px;
    overflow: hidden;
}

.room-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: var(--bg-secondary);
    border-bottom: 2px solid var(--border-color);
}

.room-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.room-meta {
    font-size: 13px;
    color: var(--text-secondary);
}

.pc-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 16px;
    padding: 20px 24px;
}

/* PC Card Design */
.pc-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pc-card:hover {
    transform: translateY(-3px);
}

.pc-card:hover .pc-status-box {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.pc-card.selected .pc-status-box {
    box-shadow: 0 0 0 3px #10b981, 0 8px 25px rgba(16, 185, 129, 0.3);
}

.pc-name-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
    text-align: center;
}

.pc-status-box {
    flex-direction: column;
    gap: 4px;
}

.pc-time-remaining {
    font-size: 10px;
    font-weight: 600;
    opacity: 0.9;
}

.pc-status-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.pc-status-box {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 8px;
    transition: all 0.2s ease;
}

/* Available State - Green */
.pc-card.available .pc-status-box {
    border: 3px solid #10b981;
    color: #10b981;
    background: rgba(16, 185, 129, 0.05);
}

/* Booked State - Red */
.pc-card.booked .pc-status-box {
    border: 3px solid #ef4444;
    color: #ef4444;
    background: rgba(239, 68, 68, 0.05);
}

/* Temporary State - Orange */
.pc-card.temporary .pc-status-box,
.pc-card.maintenance .pc-status-box {
    border: 3px solid #f59e0b;
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.05);
}

/* Selection Checkbox */
.pc-checkbox {
    position: absolute;
    top: 32px;
    right: 8px;
    width: 22px;
    height: 22px;
    border: 2px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-card);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 10;
}

.pc-card.available .pc-checkbox {
    border-color: #10b981;
}

.pc-checkbox.checked {
    background: #10b981;
    border-color: #10b981;
}

.pc-checkbox.checked::after {
    content: "✓";
    color: white;
    font-size: 14px;
    font-weight: bold;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 18px;
    color: var(--text-primary);
    margin-bottom: 8px;
}

/* Compact View - Rooms as Grids */
#defaultView.compact-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
}

#defaultView.compact-view .room-container {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
}

#defaultView.compact-view .room-header {
    padding: 10px 14px;
    flex-shrink: 0;
}

#defaultView.compact-view .room-title {
    font-size: 13px;
}

#defaultView.compact-view .pc-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
    gap: 8px;
    padding: 10px 14px;
    flex: 1;
}

#defaultView.compact-view .room-header {
    padding: 12px 16px;
}

#defaultView.compact-view .room-title {
    font-size: 14px;
}

#defaultView.compact-view .pc-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
    padding: 12px 16px;
}

#defaultView.compact-view .pc-card {
    gap: 4px;
}

#defaultView.compact-view .pc-name-label {
    font-size: 10px;
}

#defaultView.compact-view .pc-status-box {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 8px;
    padding: 4px;
}

#defaultView.compact-view .pc-status-label {
    font-size: 8px;
}

#defaultView.compact-view .pc-time-remaining {
    font-size: 8px;
}

#defaultView.compact-view .pc-checkbox {
    display: none;
}

/* Compact View for Available Only View */
#toggleView.compact-view .available-rooms-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

#toggleView.compact-view .available-room-card {
    padding: 12px 16px;
}

#toggleView.compact-view .available-room-title {
    font-size: 12px;
    margin-bottom: 10px;
    padding-bottom: 8px;
}

#toggleView.compact-view .available-pc-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
    gap: 8px;
}

#toggleView.compact-view .available-pc-chip {
    padding: 6px 10px;
    font-size: 10px;
}

#toggleView.compact-view .available-pc-chip .chip-checkbox {
    display: none;
}

/* List View for Default View */
#defaultView.list-view .pc-row {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

#defaultView.list-view .pc-card {
    flex-direction: row;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border-radius: 8px;
}

#defaultView.list-view .pc-status-box {
    width: auto;
    aspect-ratio: auto;
    padding: 8px 16px;
}

#defaultView.list-view .pc-checkbox {
    position: relative;
    top: auto;
    right: auto;
}

/* List View for Available Only View */
#toggleView.list-view .available-rooms-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

#toggleView.list-view .available-room-card {
    padding: 16px;
}

#toggleView.list-view .available-pc-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
}

#toggleView.list-view .available-pc-chip {
    padding: 8px 12px;
    text-align: center;
}

#toggleView.list-view .available-pc-chip .chip-checkbox {
    display: none;
}

/* Available Only View */
.available-rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.available-room-card {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    padding: 20px;
}

.available-room-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
}

.available-pc-list {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.available-pc-chip {
    position: relative;
    padding: 10px 16px;
    border: 2px solid #10b981;
    border-radius: 8px;
    background: rgba(16, 185, 129, 0.05);
    color: #10b981;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.available-pc-chip:hover {
    background: #10b981;
    color: white;
    transform: scale(1.05);
}

.available-pc-chip.selected {
    background: #10b981;
    color: white;
    transform: scale(1.05);
}

.available-pc-chip .chip-checkbox {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 18px;
    height: 18px;
    border: 2px solid #10b981;
    border-radius: 4px;
    background: var(--bg-card);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.available-pc-chip.selected .chip-checkbox {
    background: white;
    color: #10b981;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.modal-overlay.show {
    display: flex;
}

.modal-content {
    background: var(--bg-card);
    border-radius: 16px;
    padding: 24px;
    max-width: 400px;
    width: 90%;
    box-shadow: var(--shadow-lg);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: var(--text-secondary);
    cursor: pointer;
    line-height: 1;
}

.modal-close:hover {
    color: var(--text-primary);
}

.modal-body {
    margin-bottom: 20px;
}

.modal-detail {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.modal-detail:last-child {
    border-bottom: none;
}

.modal-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.modal-value {
    color: var(--text-primary);
    font-weight: 500;
    font-size: 14px;
}

.modal-footer {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

/* Time Filter Bar */
.time-filter-bar {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
}

.time-filter-form {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 16px;
}

.filter-group {
    flex: 1;
    min-width: 300px;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.filter-label i {
    color: var(--accent-primary);
}

.filter-inputs {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}

.filter-input-wrapper {
    flex: 1;
    min-width: 140px;
}

.filter-hours-wrapper {
    min-width: 140px;
}

.filter-or-divider {
    padding: 10px 16px;
    color: var(--text-muted);
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.filter-input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    font-size: 14px;
    background: var(--bg-primary);
    color: var(--text-primary);
    cursor: pointer;
}

.filter-input:focus {
    outline: none;
    border-color: var(--accent-primary);
}

.filter-btn {
    padding: 10px 20px;
    background: var(--accent-primary);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    background: var(--accent-secondary);
    transform: translateY(-1px);
}

.filter-clear-btn {
    padding: 10px 20px;
    background: var(--bg-secondary);
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.filter-clear-btn:hover {
    border-color: var(--danger);
    color: var(--danger);
}

/* Active Filter Indicator */
.filter-active-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid var(--accent-primary);
    border-radius: 20px;
    font-size: 12px;
    color: var(--accent-primary);
    font-weight: 500;
    margin-left: 12px;
}

/* Status Guide Bar */
.status-guide-bar {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 16px 24px;
    margin-bottom: 24px;
}

.guide-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.guide-title i {
    color: var(--accent-primary);
}

.guide-items {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.guide-item {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 250px;
    padding: 10px 14px;
    background: var(--bg-secondary);
    border-radius: 8px;
}

.guide-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    flex-shrink: 0;
}

.guide-dot.available { background: #10b981; }
.guide-dot.booked { background: #ef4444; }
.guide-dot.temporary { background: #f59e0b; }

.guide-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-primary);
    white-space: nowrap;
}

.guide-desc {
    font-size: 11px;
    color: var(--text-secondary);
    flex: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .status-topbar {
        flex-direction: column;
        gap: 16px;
    }
    
    .selection-bar {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
    
    .legend-badges {
        display: none;
    }
    
    .pc-row {
        justify-content: center;
    }
    
    .pc-card {
        width: 100px;
    }
    
    .available-rooms-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-inputs {
        flex-direction: column;
    }
    
    .filter-input-wrapper {
        width: 100%;
    }
    
    .guide-items {
        flex-direction: column;
        gap: 10px;
    }
    
    .guide-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
        padding: 12px;
    }
    
    .guide-dot {
        width: 12px;
        height: 12px;
    }
    
    .guide-label {
        font-size: 11px;
    }
    
    .guide-desc {
        font-size: 10px;
        line-height: 1.4;
    }
}
</style>
@endsection

@section('content')
<div class="pc-dashboard">
    <!-- Selection Bar -->
    <div class="selection-bar" id="selectionBar">
        <div class="selection-info">
            <i class="fas fa-check-circle"></i>
            <span class="selection-count"><span id="selectedCount">0</span> PC(s) selected</span>
        </div>
        <div class="selection-actions">
            <button class="btn-select-action" onclick="clearSelection()">Clear</button>
            <button class="btn-select-action" onclick="selectAllAvailable()">Select All Available</button>
            <button class="btn-book-selected" onclick="bookSelected()">
                <i class="fas fa-calendar-check"></i>
                Book Selected
            </button>
        </div>
    </div>

     <!-- PC Status Guide -->
    <div class="status-guide-bar">
        <div class="guide-title">
            <i class="fas fa-info-circle"></i>
            PC Status Guide
        </div>
        <div class="guide-items">
            <div class="guide-item">
                <span class="guide-dot booked"></span>
                <span class="guide-label">Booked (Red)</span>
                <span class="guide-desc">PC is booked and not available. Wait until booking ends.</span>
            </div>
            <div class="guide-item">
                <span class="guide-dot temporary"></span>
                <span class="guide-label">Pending Approval (Orange)</span>
                <span class="guide-desc">Someone booked it. Admin must approve within 15 min or it becomes available.</span>
            </div>
            <div class="guide-item">
                <span class="guide-dot available"></span>
                <span class="guide-label">Available (Green)</span>
                <span class="guide-desc">PC is free! Book it now. It will turn orange until admin approves.</span>
            </div>
        </div>
    </div>

    <!-- Top Bar with Toggle & View Mode -->
    <div class="status-topbar">
        <div class="topbar-left">
            <div class="status-toggle">
                <span class="status-toggle-label">Show Available Only</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="filterToggle" onchange="filterAvailable()">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
        <div class="topbar-right">
            <div class="view-toggle">
                <span class="view-toggle-label">View:</span>
                <div class="view-buttons">
                    <button class="view-btn active" id="gridViewBtn" onclick="setViewMode('grid')" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" id="compactViewBtn" onclick="setViewMode('compact')" title="Compact View">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-btn" id="listViewBtn" onclick="setViewMode('list')" title="List View">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            <div class="legend-badges">
                <div class="legend-badge">
                    <span class="legend-dot available"></span>
                    Available
                </div>
                <div class="legend-badge">
                    <span class="legend-dot booked"></span>
                    Booked
                </div>
                <div class="legend-badge">
                    <span class="legend-dot temporary"></span>
                    Temporary Hold
                </div>
            </div>
        </div>
    </div>

    <!-- Time Range Filter -->
    <div class="time-filter-bar">
        <form action="{{ route('website.booking.pc-status') }}" method="GET" id="timeFilterForm" class="time-filter-form">
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-clock"></i>
                    Filter by Time Slot
                </label>
                <div class="filter-inputs">
                    <div class="filter-input-wrapper">
                        <input type="date" name="filter_date" id="filterDate" class="filter-input" value="{{ $filterDate ?? date('Y-m-d') }}" required>
                    </div>
                    <div class="filter-input-wrapper">
                        <select name="filter_start" id="filterStart" class="filter-input filter-start" required>
                            <option value="">Start Time</option>
                            @for($hour = 0; $hour < 24; $hour++)
                                @for($min = 0; $min < 60; $min += 30)
                                    <option value="{{ sprintf('%02d:%02d', $hour, $min) }}" {{ ($filterStart ?? '') == sprintf('%02d:%02d', $hour, $min) ? 'selected' : '' }}>
                                        {{ date('h:i A', strtotime(sprintf('%02d:%02d', $hour, $min))) }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                    <div class="filter-input-wrapper filter-hours-wrapper">
                        <select name="filter_hours" class="filter-input" id="filterHours">
                            <option value="">Duration</option>
                            @for($h = 1; $h <= 12; $h++)
                                <option value="{{ $h }}" {{ ($filterHours ?? '') == $h ? 'selected' : '' }}>
                                    {{ $h }} hour{{ $h > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-or-divider">OR</div>
                    <div class="filter-input-wrapper">
                        <select name="filter_end" class="filter-input">
                            <option value="">End Time</option>
                            @for($hour = 0; $hour < 24; $hour++)
                                @for($min = 0; $min < 60; $min += 30)
                                    <option value="{{ sprintf('%02d:%02d', $hour, $min) }}" {{ ($filterEnd ?? '') == sprintf('%02d:%02d', $hour, $min) ? 'selected' : '' }}>
                                        {{ date('h:i A', strtotime(sprintf('%02d:%02d', $hour, $min))) }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    @if(isset($filterStart))
                        <a href="{{ route('website.booking.pc-status') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

   
    <!-- Default View: All Rooms -->
    <div id="defaultView">
        @forelse($rooms as $room)
        <div class="room-container">
            <div class="room-header">
                <div class="room-title">{{ $room->name }}</div>
                <div class="room-meta">{{ $room->pcs->count() }} PCs</div>
            </div>
            <div class="pc-row">
                @forelse($room->pcs as $pc)
                    @php
                        $status = $pcStatuses[$pc->id] ?? null;
                        $pcStatus = $status ? $status['status'] : 'available';
                        $pcUser = $status['user'] ?? null;
                        $pcMinutes = $status['minutes_remaining'] ?? null;
                        
                        // Determine status text - only for booked/temporary show time
                        $statusText = '';
                        $timeText = '';
                        if ($pcStatus === 'booked' && $pcMinutes !== null) {
                            $hours = floor($pcMinutes / 60);
                            $mins = ceil($pcMinutes % 60);
                            if ($hours > 0) {
                                $timeText = "Free in {$hours}h {$mins}m";
                            } else {
                                $timeText = "Free in {$mins}m";
                            }
                        } elseif ($pcStatus === 'temporary' && $pcMinutes !== null) {
                            $timeText = "Unlock " . ceil($pcMinutes) . "m";
                        }
                    @endphp
                    <div class="pc-card {{ $pcStatus }}" data-pc-id="{{ $pc->id }}" data-pc-name="{{ $pc->name }}" data-pc-status="{{ $pcStatus }}" onclick="handlePcClick(this, {{ $pc->id }}, '{{ $pc->name }}', '{{ $pcStatus }}', '{{ $pcUser ?? '' }}', {{ $pcMinutes ?? 'null' }})">
                        <div class="pc-checkbox" onclick="event.stopPropagation(); toggleCheckbox(this)"></div>
                        <div class="pc-name-label">{{ $pc->name }}</div>
                        <div class="pc-status-box">
                            @if($timeText)
                                <div class="pc-time-remaining">{{ $timeText }}</div>
                            @else
                                <div class="pc-status-label">AVAILABLE</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-desktop"></i>
                        <p>No PCs in this room</p>
                    </div>
                @endforelse
            </div>
        </div>
        @empty
        <div class="room-container">
            <div class="empty-state">
                <i class="fas fa-building"></i>
                <h3>No Rooms Available</h3>
                <p>There are no active gaming rooms at this time.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Toggle View: Available PCs Only -->
    <div id="toggleView" style="display: none;">
        <div class="available-rooms-grid">
            @php
                $availableRooms = collect($rooms)->map(function($room) use ($pcStatuses) {
                    $availablePcs = $room->pcs->filter(function($pc) use ($pcStatuses) {
                        $status = data_get($pcStatuses, $pc->id . '.status', 'available');
                        return $status === 'available';
                    });
                    return [
                        'id' => $room->id,
                        'name' => $room->name,
                        'pcs' => $availablePcs->values()
                    ];
                })->filter(function($room) {
                    return $room['pcs']->count() > 0;
                });
            @endphp
            
            @forelse($availableRooms as $room)
            <div class="available-room-card">
                <div class="available-room-title">{{ $room['name'] }}</div>
                <div class="available-pc-list">
                    @foreach($room['pcs'] as $pc)
                    <div class="available-pc-chip" data-pc-id="{{ $pc->id }}" onclick="toggleChipSelection(this, {{ $pc->id }})">
                        {{ $pc->name }}
                        <span class="chip-checkbox"><i class="fas fa-check"></i></span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="room-container">
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h3>All PCs Booked</h3>
                    <p>No PCs currently available. Try again later!</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- PC Details Modal -->
<div class="modal-overlay" id="pcModal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="modalPcName">PC Details</h4>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-detail">
                <span class="modal-label">Status</span>
                <span class="modal-value" id="modalStatus">-</span>
            </div>
            <div class="modal-detail">
                <span class="modal-label">Booked By</span>
                <span class="modal-value" id="modalUser">-</span>
            </div>
            <div class="modal-detail">
                <span class="modal-label">Time Remaining</span>
                <span class="modal-value" id="modalTime">-</span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary-custom" onclick="closeModal()">Close</button>
            <button class="btn-primary-custom" id="modalBookBtn" onclick="bookFromModal()">
                <i class="fas fa-calendar-plus"></i>
                Book This PC
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let selectedPcId = null;
let selectedPcIds = [];

// Handle PC card click - clicking anywhere on available PC selects it
function handlePcClick(element, pcId, pcName, status, user, minutes) {
    if (status !== 'available') {
        // Show modal for booked/unavailable PCs
        showPcDetails(pcId, pcName, status, user, minutes);
    } else {
        // For available PCs, toggle selection by finding the checkbox
        const card = element.closest('.pc-card');
        const checkbox = card.querySelector('.pc-checkbox');
        toggleCheckbox(checkbox);
    }
}

// Toggle checkbox
function toggleCheckbox(checkbox) {
    const card = checkbox.closest('.pc-card');
    const pcId = card.dataset.pcId;
    const status = card.dataset.pcStatus || 'available';
    
    // Only allow selecting available PCs
    if (status !== 'available') {
        return;
    }
    
    checkbox.classList.toggle('checked');
    card.classList.toggle('selected');
    
    if (checkbox.classList.contains('checked')) {
        if (!selectedPcIds.includes(pcId)) {
            selectedPcIds.push(pcId);
        }
    } else {
        selectedPcIds = selectedPcIds.filter(id => id !== pcId);
    }
    
    updateSelectionBar();
}

// Toggle chip selection (in toggle view)
function toggleChipSelection(chip, pcId) {
    chip.classList.toggle('selected');
    
    if (chip.classList.contains('selected')) {
        if (!selectedPcIds.includes(pcId.toString())) {
            selectedPcIds.push(pcId.toString());
        }
    } else {
        selectedPcIds = selectedPcIds.filter(id => id !== pcId.toString());
    }
    
    updateSelectionBar();
}

// Update selection bar
function updateSelectionBar() {
    const selectionBar = document.getElementById('selectionBar');
    const countSpan = document.getElementById('selectedCount');
    
    countSpan.textContent = selectedPcIds.length;
    
    if (selectedPcIds.length > 0) {
        selectionBar.classList.add('show');
    } else {
        selectionBar.classList.remove('show');
    }
}

// Clear selection
function clearSelection() {
    selectedPcIds = [];
    
    // Clear all checkboxes
    document.querySelectorAll('.pc-checkbox.checked').forEach(cb => {
        cb.classList.remove('checked');
    });
    
    // Clear all selected cards
    document.querySelectorAll('.pc-card.selected').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Clear all selected chips
    document.querySelectorAll('.available-pc-chip.selected').forEach(chip => {
        chip.classList.remove('selected');
    });
    
    updateSelectionBar();
}

// Select all available
function selectAllAvailable() {
    // Clear current selection
    selectedPcIds = [];
    
    // Select all available PCs in default view
    document.querySelectorAll('.pc-card.available').forEach(card => {
        const checkbox = card.querySelector('.pc-checkbox');
        checkbox.classList.add('checked');
        card.classList.add('selected');
        selectedPcIds.push(card.dataset.pcId);
    });
    
    // Select all chips in toggle view
    document.querySelectorAll('.available-pc-chip').forEach(chip => {
        chip.classList.add('selected');
        selectedPcIds.push(chip.dataset.pcId);
    });
    
    updateSelectionBar();
}

// Book selected PCs - pass date, start time, and hours to pre-fill booking form
function bookSelected() {
    if (selectedPcIds.length > 0) {
        const ids = selectedPcIds.join(',');
        let url = '/booking/create?pc_ids=' + ids;
        
        // Get filter values if they exist (use name attribute for form fields)
        const filterDate = document.querySelector('input[name="filter_date"]')?.value;
        const filterStart = document.querySelector('select[name="filter_start"]')?.value;
        const filterHours = document.getElementById('filterHours')?.value;
        const filterEnd = document.querySelector('select[name="filter_end"]')?.value;
        
        // Add filter parameters if available
        if (filterDate) {
            url += '&date=' + filterDate;
        }
        if (filterStart) {
            url += '&start_time=' + filterStart;
        }
        if (filterHours) {
            url += '&hours=' + filterHours;
        }
        if (filterEnd) {
            url += '&end_time=' + filterEnd;
        }
        
        window.location.href = url;
    }
}

// Filter available only - show/hide PCs based on availability
function filterAvailable() {
    const toggle = document.getElementById('filterToggle');
    const showOnlyAvailable = toggle.checked;
    
    // Loop through all PC cards and hide/show based on status
    document.querySelectorAll('.pc-card').forEach(card => {
        const status = card.dataset.pcStatus;
        if (showOnlyAvailable) {
            if (status === 'available') {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        } else {
            card.style.display = '';
        }
    });
    
    // Loop through all room containers and hide if no visible PCs
    document.querySelectorAll('.room-container').forEach(room => {
        const visibleCards = room.querySelectorAll('.pc-card[style=""], .pc-card:not([style])');
        const hiddenCards = room.querySelectorAll('.pc-card[style*="display: none"]');
        if (visibleCards.length === 0) {
            room.style.display = 'none';
        } else {
            room.style.display = '';
        }
    });
    
    // Loop through available room cards and hide if no visible chips
    document.querySelectorAll('.available-room-card').forEach(card => {
        const visibleChips = card.querySelectorAll('.available-pc-chip[style=""], .available-pc-chip:not([style])');
        if (visibleChips.length === 0) {
            card.style.display = 'none';
        } else {
            card.style.display = '';
        }
    });
}

// Set view mode (grid, compact, or list)
function setViewMode(mode) {
    var gridBtn = document.getElementById('gridViewBtn');
    var compactBtn = document.getElementById('compactViewBtn');
    var listBtn = document.getElementById('listViewBtn');
    var defaultView = document.getElementById('defaultView');
    var toggleView = document.getElementById('toggleView');
    
    // Remove all active classes
    gridBtn.classList.remove('active');
    compactBtn.classList.remove('active');
    listBtn.classList.remove('active');
    defaultView.classList.remove('compact-view');
    defaultView.classList.remove('list-view');
    toggleView.classList.remove('compact-view');
    toggleView.classList.remove('list-view');
    
    if (mode === 'grid') {
        gridBtn.classList.add('active');
    } else if (mode === 'compact') {
        compactBtn.classList.add('active');
        defaultView.classList.add('compact-view');
        toggleView.classList.add('compact-view');
    } else if (mode === 'list') {
        listBtn.classList.add('active');
        defaultView.classList.add('list-view');
        toggleView.classList.add('list-view');
    }
    
    // Save to session via API
    fetch('/booking/set-view-mode', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ view_mode: mode })
    });
}

// Apply saved view mode from session on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($viewMode) && $viewMode !== 'grid')
        setViewMode('{{ $viewMode }}');
    @endif
});

// Show PC details modal
function showPcDetails(pcId, pcName, status, user, minutes) {
    selectedPcId = pcId;
    
    document.getElementById('modalPcName').textContent = pcName;
    
    // Format status display
    let statusDisplay = status.charAt(0).toUpperCase() + status.slice(1);
    if (status === 'booked' && minutes) {
        const hours = Math.floor(minutes / 60);
        const mins = Math.ceil(minutes % 60);
        if (hours > 0) {
            statusDisplay = `Booked (Free in ${hours}h ${mins}m)`;
        } else {
            statusDisplay = `Booked (Free in ${mins} min)`;
        }
    } else if (status === 'temporary' && minutes) {
        statusDisplay = `Temporary Hold (Unlock in ${Math.ceil(minutes)} min)`;
    }
    document.getElementById('modalStatus').textContent = statusDisplay;
    
    document.getElementById('modalUser').textContent = user || '-';
    document.getElementById('modalTime').textContent = minutes ? Math.ceil(minutes) + ' minutes' : '-';
    
    const bookBtn = document.getElementById('modalBookBtn');
    if (status === 'available') {
        bookBtn.style.display = 'block';
    } else {
        bookBtn.style.display = 'none';
    }
    
    document.getElementById('pcModal').classList.add('show');
}

function closeModal() {
    document.getElementById('pcModal').classList.remove('show');
    selectedPcId = null;
}

function bookFromModal() {
    if (selectedPcId) {
        window.location.href = '/booking/create?pc_id=' + selectedPcId;
    }
}

// Close modal on outside click
document.getElementById('pcModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Form validation - ensure either duration OR end time is selected
document.getElementById('timeFilterForm').addEventListener('submit', function(e) {
    const startTime = document.querySelector('select[name="filter_start"]').value;
    const duration = document.getElementById('filterHours').value;
    const endTime = document.querySelector('select[name="filter_end"]').value;
    
    if (startTime && !duration && !endTime) {
        e.preventDefault();
        alert('Please select either Duration or End Time to complete the time slot.');
        return false;
    }
    
    if (startTime && duration && endTime) {
        e.preventDefault();
        alert('Please select either Duration OR End Time, not both. Choose one option only.');
        return false;
    }
    
    return true;
});

// Clear end time when duration is selected and vice versa
document.getElementById('filterHours').addEventListener('change', function() {
    if (this.value) {
        document.querySelector('select[name="filter_end"]').value = '';
    }
});

document.querySelector('select[name="filter_end"]').addEventListener('change', function() {
    if (this.value) {
        document.getElementById('filterHours').value = '';
    }
});
</script>
@endsection
