@extends('layouts.app')

@section('title', 'Booking Confirmation - Gaming Zone')
@section('page_title', 'Booking Confirmation')

@section('extra_styles')
<style>
.confirmation-container {
    max-width: 1200px;
    margin: 0 auto;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    animation: scaleIn 0.5s ease;
}

@keyframes scaleIn {
    from { transform: scale(0); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.success-icon i {
    font-size: 48px;
    color: white;
}

.confirmation-title {
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.confirmation-subtitle {
    text-align: center;
    color: var(--text-secondary);
    margin-bottom: 32px;
}

.booking-card {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 24px;
}

.booking-card-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    padding: 16px 24px;
    border-bottom: none;
}

.booking-card-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: white;
}

.booking-card-body {
    padding: 24px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.detail-value {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 14px;
}

.detail-value.success {
    color: #10b981;
}

.action-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-bottom: 24px;
}

/* Screenshot Section */
.screenshot-section {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
}

.screenshot-section h5 {
    text-align: center;
    margin-bottom: 16px;
    color: var(--text-primary);
    font-size: 16px;
    font-weight: 600;
}

#booking-screenshot {
    background: var(--bg-primary);
    padding: 24px;
    border-radius: 12px;
    border: 2px dashed var(--border-color);
}

.screenshot-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 16px;
}

.btn-whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-whatsapp:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
}

.btn-whatsapp i {
    font-size: 20px;
}

.btn-screenshot {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-screenshot:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
}

.count-badge {
    background: var(--accent-primary);
    color: white;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
}
</style>
@endsection

@section('content')
<div class="breadcrumb-custom">
    <a href="{{ route('home') }}">Home</a>
    <i class="fas fa-chevron-right"></i>
    <span>Booking Confirmation</span>
</div>

<div class="confirmation-container">
    @php
        $allBookings = $groupBookings;
        $pcCount = $allBookings->count();
        $totalAmount = $allBookings->sum('total_amount');
        $pcNames = $allBookings->map(function($b) {
            return $b->pc ? $b->pc->name : 'Unknown';
        })->filter()->unique()->values();
        $displayId = $booking->booking_group_id ?? 'BG-' . $booking->id;
        $showSuccess = session('success') || ($booking && $booking->exists && $pcCount > 0);
    @endphp
    
    @if($showSuccess)
    <div class="success-icon">
        <i class="fas fa-check"></i>
    </div>
    
    <h1 class="confirmation-title">Booking Confirmed!</h1>
    <p class="confirmation-subtitle">Your booking has been submitted and is awaiting approval.</p>
     <div class="row"> 
    <!-- Screenshot Section -->
    <div class="col-md-6 g-4" >
    <div class="screenshot-section" >
        <h5><i class="fas fa-share-alt me-2"></i>Share Your Booking</h5>
        <div id="booking-screenshot">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-gamepad" style="font-size: 24px; color: #6366f1;"></i>
                    <span style="font-size: 20px; font-weight: 700; color: var(--text-primary);">Gaming Zone</span>
                </div>
            </div>
            
            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px; padding: 16px; text-align: center; margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="font-size: 32px; color: white;"></i>
                <div style="font-size: 18px; font-weight: 700; color: white; margin-top: 8px;">Booking Confirmed!</div>
            </div>
            
            <div style="background: var(--bg-card); border-radius: 12px; overflow: hidden; border: 2px solid var(--border-color);">
                <div style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 14px 20px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-ticket-alt" style="color: white;"></i>
                        <span style="color: white; font-weight: 600;">Booking #{{ $booking->booking_group_id ?? 'BG-' . $booking->id }}</span>
                    </div>
                </div>
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">Room</span>
                        <span style="font-weight: 600; color: var(--text-primary);">{{ $booking->room->name ?? 'N/A' }}</span>
                    </div>
                    @if($pcCount > 1)
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">PCs</span>
                        <span style="font-weight: 600; color: var(--text-primary);">{{ $pcNames->implode(', ') }}</span>
                    </div>
                    @else
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">PC</span>
                        <span style="font-weight: 600; color: var(--text-primary);">{{ $booking->pc->name ?? 'N/A' }}</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">Date</span>
                        <span style="font-weight: 600; color: var(--text-primary);">{{ $booking->start_time->format('M d, Y') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">Time</span>
                        <span style="font-weight: 600; color: var(--text-primary);">{{ $booking->start_time->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">Duration</span>
                        <span style="font-weight: 600; color: var(--text-primary);">{{ $booking->hours }} hour(s)</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">Status</span>
                        <span style="font-weight: 600; color: #f59e0b;">Pending Approval</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; background: #fef3c7; margin: 12px -20px -20px; padding: 16px 20px; border-radius: 0 0 10px 10px;">
                        <span style="font-weight: 600; color: #92400e;">Total Amount</span>
                        <span style="font-weight: 700; color: #059669; font-size: 18px;">${{ number_format($totalAmount, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 16px; color: var(--text-secondary); font-size: 12px;">
                <i class="fas fa-clock me-1"></i> Pending Approval - Show this to staff
            </div>
        </div>
        
        <div class="screenshot-actions">
            <button onclick="downloadScreenshot()" class="btn-screenshot">
                <i class="fas fa-download"></i>
                Download
            </button>
            <button onclick="shareOnWhatsApp()" class="btn-whatsapp">
                <i class="fab fa-whatsapp"></i>
                Share on WhatsApp
            </button>
        </div>
    </div>
    </div>
 
    <div class="col-md-6"  >
    <div class="booking-card"  >
        <div class="booking-card-header">
            <h5>
                <i class="fas fa-list-alt me-2"></i>
                Booking Details
            </h5>
        </div>
        <div class="booking-card-body">
            <div class="detail-row">
                <span class="detail-label">Booking ID</span>
                <span class="detail-value">#{{ $displayId }}</span>
            </div>
            @if($pcCount > 1)
            <div class="detail-row">
                <span class="detail-label">PCs Booked</span>
                <span class="detail-value">{{ $pcNames->implode(', ') }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Room</span>
                <span class="detail-value">{{ $booking->room->name ?? 'N/A' }}</span>
            </div>
            @if($pcCount == 1)
            <div class="detail-row">
                <span class="detail-label">PC</span>
                <span class="detail-value">{{ $booking->pc->name ?? 'N/A' }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value">{{ $booking->start_time->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time</span>
                <span class="detail-value">{{ $booking->start_time->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Duration</span>
                <span class="detail-value">{{ $booking->hours }} hour(s)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount</span>
                <span class="detail-value success">${{ number_format($totalAmount, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="badge badge-warning">Pending Approval</span>
                </span>
            </div>
        </div>
    </div>
    </div>
    </div>
    
    <div class="alert-info-custom" style="margin-bottom: 24px;">
        <i class="fas fa-info-circle"></i>
        <strong>What's Next?</strong><br>
        Your booking is currently pending approval. You'll be notified once an administrator approves it. You can view your booking status in the My Bookings section.
    </div>
    
    <div class="action-buttons">
        <a href="{{ route('website.booking.my-bookings') }}" class="btn-secondary-custom">
            <i class="fas fa-list me-2"></i>
            My Bookings
        </a>
        <a href="{{ route('home') }}" class="btn-primary-custom">
            <i class="fas fa-home me-2"></i>
            Go to Home
        </a>
    </div>
    @else
    <div class="card-custom">
        <div class="card-body-custom" style="text-align: center; padding: 60px 24px;">
            <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: var(--text-muted);"></i>
            <h4>No Booking Found</h4>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">You haven't made any bookings yet.</p>
            <a href="{{ route('website.booking.pc-status') }}" class="btn-primary-custom">
                <i class="fas fa-desktop me-2"></i>
                View PC Status
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Include html2canvas for screenshot functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
async function downloadScreenshot() {
    const screenshotDiv = document.getElementById('booking-screenshot');
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    btn.disabled = true;
    
    try {
        const canvas = await html2canvas(screenshotDiv, {
            backgroundColor: '#0f172a',
            scale: 2,
            logging: false
        });
        
        const link = document.createElement('a');
        link.download = 'booking-{{ $booking->id }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        
        btn.innerHTML = '<i class="fas fa-check"></i> Downloaded!';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 2000);
    } catch (error) {
        alert('Failed to generate screenshot');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

async function shareOnWhatsApp() {
    const screenshotDiv = document.getElementById('booking-screenshot');
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    btn.disabled = true;
    
    try {
        const canvas = await html2canvas(screenshotDiv, {
            backgroundColor: '#0f172a',
            scale: 2,
            logging: false
        });
        
        // Convert to blob
        canvas.toBlob(async function(blob) {
            if (blob) {
                // Try to share using Web Share API with files
                if (navigator.share && navigator.canShare) {
                    const file = new File([blob], 'booking.png', { type: 'image/png' });
                    if (navigator.canShare({ files: [file] })) {
                        try {
                            await navigator.share({
                                files: [file],
                                title: 'My Gaming Zone Booking',
                                text: 'Check out my booking at Gaming Zone! #{{ $booking->id }}'
                            });
                            btn.innerHTML = '<i class="fas fa-check"></i> Shared!';
                        } catch (e) {
                            // Fall back to WhatsApp link
                            openWhatsAppDirect();
                        }
                    } else {
                        openWhatsAppDirect();
                    }
                } else {
                    openWhatsAppDirect();
                }
            }
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 'image/png');
    } catch (error) {
        alert('Failed to generate screenshot');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

function openWhatsAppDirect() {
    const bookingId = '{{ $booking->booking_group_id ?? "BG-" . $booking->id }}';
    const message = encodeURIComponent('🎮 My Gaming Zone Booking #' + bookingId + '\n\n' +
        '📍 Room: {{ $booking->room->name ?? "N/A" }}\n' +
        '💻 PC(s): {{ $pcCount > 1 ? $pcNames->implode(", ") : ($booking->pc->name ?? "N/A") }}\n' +
        '📅 Date: {{ $booking->start_time->format("M d, Y") }}\n' +
        '⏰ Time: {{ $booking->start_time->format("h:i A") }} - {{ $booking->end_time->format("h:i A") }}\n' +
        '⏱️ Duration: {{ $booking->hours }} hour(s)\n' +
        '💰 Total: ${{ number_format($totalAmount, 2) }}\n\n' +
        'Status: ⏳ Pending Approval\n\n' +
        'Sent from Gaming Zone App');
    
    window.open('https://wa.me/?text=' + message, '_blank');
}
</script>
@endsection
