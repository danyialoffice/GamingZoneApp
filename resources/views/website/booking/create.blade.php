@extends('layouts.app')

@section('title', 'Book PCs - Gaming Zone')
@section('page_title', 'Book Gaming PCs')

@php
    $preselectedRoom = $selectedPcs->first()?->room_id ?? null;
    $preselectedPcIds = $selectedPcs->pluck('id')->toArray();
    $isPreselected = count($preselectedPcIds) > 0;
    
    // Calculate hours from end_time if provided
    $calculatedHours = '';
    if (!empty($prefilledEndTime) && !empty($prefilledStartTime)) {
        $startParts = explode(':', $prefilledStartTime);
        $endParts = explode(':', $prefilledEndTime);
        $startMinutes = (int)$startParts[0] * 60 + (int)($startParts[1] ?? 0);
        $endMinutes = (int)$endParts[0] * 60 + (int)($endParts[1] ?? 0);
        $diffMinutes = $endMinutes - $startMinutes;
        if ($diffMinutes > 0) {
            $calculatedHours = ceil($diffMinutes / 60);
        }
    }
    
    // Use prefilled hours if available, otherwise use calculated hours from end_time
    $finalPrefilledHours = !empty($prefilledHours) ? $prefilledHours : $calculatedHours;
@endphp

@section('extra_styles')
<style>
.booking-summary-card {
    background: var(--bg-secondary);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
}
.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--bg-primary);
    border-radius: 10px;
}
.summary-item.total {
    grid-column: span 2;
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: white;
}
.summary-label {
    color: var(--text-secondary);
}
.summary-item.total .summary-label,
.summary-item.total .summary-value {
    color: white;
    font-weight: 600;
}
.summary-value {
    font-weight: 600;
    color: var(--text-primary);
}
.selected-pcs-display {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}
.pc-tag {
    padding: 8px 16px;
    background: rgba(16, 185, 129, 0.1);
    border: 2px solid #10b981;
    border-radius: 8px;
    color: #10b981;
    font-weight: 600;
    font-size: 14px;
}
.no-selection-message {
    text-align: center;
    padding: 60px 24px;
    background: var(--bg-card);
    border: 2px dashed var(--border-color);
    border-radius: 16px;
}
.no-selection-message i {
    font-size: 48px;
    color: var(--text-muted);
    margin-bottom: 16px;
}
.no-selection-message h4 {
    margin-bottom: 8px;
    color: var(--text-primary);
}
.no-selection-message p {
    color: var(--text-secondary);
    margin-bottom: 24px;
}
</style>
@endsection

@section('content')
<div class="breadcrumb-custom">
    <a href="{{ route('home') }}">Home</a>
    <i class="fas fa-chevron-right"></i>
    <span>Book PCs</span>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h5 class="card-title-custom">
            <i class="fas fa-calendar-plus me-2" style="color: var(--accent-primary);"></i>
            Book Gaming PCs
        </h5>
    </div>
    <div class="card-body-custom">
        @if(session('success'))
            <div class="alert-success-custom">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert-error-custom">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(!$isPreselected)
            <!-- No PCs selected - Show message to go to PC Status -->
            <div class="no-selection-message">
                <i class="fas fa-desktop"></i>
                <h4>No PCs Selected</h4>
                <p>Please select PCs from the PC Status page to proceed with booking.</p>
                <a href="{{ route('website.booking.pc-status') }}" class="btn-primary-custom">
                    <i class="fas fa-tv me-2"></i>
                    Go to PC Status
                </a>
            </div>
        @else
        <form action="{{ route('website.booking.store') }}" method="POST" id="bookingForm">
            @csrf
            
            <!-- Hidden fields for pre-selected PCs -->
            <input type="hidden" name="pc_ids" id="selectedPcIds" value="{{ json_encode($preselectedPcIds) }}">
            
            <!-- Show selected PCs info -->
            <div class="booking-summary-card">
                <h5 style="margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-desktop" style="color: var(--accent-primary);"></i>
                    Selected PCs
                </h5>
                <div class="selected-pcs-display">
                    @foreach($selectedPcs as $pc)
                        <span class="pc-tag">
                            <i class="fas fa-desktop me-1"></i>
                            {{ $pc->name }}
                        </span>
                    @endforeach
                </div>
            </div>

  <div class="row" >
  <div class="col-md-6" >
            <!-- Booking Details -->
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar" style="color: var(--accent-primary);"></i>
                            Booking Date
                        </label>
                        <input type="date" class="form-control-custom" name="date" value="{{ old('date', $prefilledDate ?? date('Y-m-d')) }}" required min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock" style="color: var(--accent-primary);"></i>
                            Start Time
                        </label>
                        <select class="form-select-custom" name="start_time" required>
                            <option value="">Select Time</option>
                            @for($hour = 0; $hour <= 24; $hour++)
                                <option value="{{ sprintf('%02d:00', $hour) }}" {{ old('start_time', $prefilledStartTime ?? '') == sprintf('%02d:00', $hour) ? 'selected' : '' }}>
                                    {{ date('h:i A', strtotime(sprintf('%02d:00', $hour))) }}
                                </option>
                                @if($hour < 22)
                                <option value="{{ sprintf('%02d:30', $hour) }}" {{ old('start_time', $prefilledStartTime ?? '') == sprintf('%02d:30', $hour) ? 'selected' : '' }}>
                                    {{ date('h:i A', strtotime(sprintf('%02d:30', $hour))) }}
                                </option>
                                @endif
                            @endfor
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-hourglass-half" style="color: var(--accent-primary);"></i>
                            Duration (Hours)
                        </label>
                        <select class="form-select-custom" name="hours" required id="durationSelect">
                            <option value="">Select Duration</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('hours', $finalPrefilledHours ?? '') == $i ? 'selected' : '' }}>
                                    {{ $i }} hour{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-sticky-note" style="color: var(--accent-primary);"></i>
                            Notes (Optional)
                        </label>
                        <input type="text" class="form-control-custom" name="notes" value="{{ old('notes') }}" placeholder="Any special requirements?">
                    </div>
                </div>
            </div>
               </div>
                 <div class="col-md-6" >
            <!-- Booking Summary -->
            <div class="booking-summary-card mt-4" id="bookingSummary">
                <h5 style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-receipt" style="color: var(--accent-primary);"></i>
                    Booking Summary
                </h5>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">Selected PCs:</span>
                        <span class="summary-value" id="summaryPcs">{{ count($preselectedPcIds) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Rate:</span>
                        <span class="summary-value" id="summaryRate">${{ number_format($rooms->find($preselectedRoom)?->hourly_rate ?? 0, 2) }}/hr</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Duration:</span>
                        <span class="summary-value" id="summaryDuration">0 hrs</span>
                    </div>
                    <div class="summary-item total">
                        <span class="summary-label">Total:</span>
                        <span class="summary-value" id="summaryTotal">$0.00</span>
                    </div>
                </div>
            </div>
   </div>
   </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;" class="mt-4">
                <a href="{{ route('website.booking.pc-status') }}" class="btn-secondary-custom">
                    Cancel
                </a>
                <button type="submit" class="btn-primary-custom" id="submitBtn">
                    <i class="fas fa-calendar-check"></i>
                    Confirm Booking
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedPcs = @json($preselectedPcIds);
let roomRate = {{ $rooms->find($preselectedRoom)?->hourly_rate ?? 0 }};

function updateSummary() {
    document.getElementById('summaryPcs').textContent = selectedPcs.length;
    document.getElementById('summaryRate').textContent = '$' + roomRate.toFixed(2) + '/hr';
    document.getElementById('summaryDuration').textContent = (document.getElementById('durationSelect').value || 0) + ' hrs';
    
    const hours = parseInt(document.getElementById('durationSelect').value) || 0;
    const total = selectedPcs.length * roomRate * hours;
    document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);
}

document.getElementById('durationSelect').addEventListener('change', updateSummary);

// Update summary on page load with prefilled values
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});
</script>
@endsection
