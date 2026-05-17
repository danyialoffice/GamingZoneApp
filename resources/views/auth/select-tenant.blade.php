@extends('layouts.app')

@section('title', 'Select Gaming Zone')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-gamepad"></i>
            <h2>Select Gaming Zone</h2>
            <p>Choose a gaming zone to continue</p>
        </div>

        <div class="auth-body">
            @if(\App\Models\Tenant::where('status', 'active')->count() > 0)
                <div class="tenant-list">
                    @foreach(\App\Models\Tenant::where('status', 'active')->get() as $tenant)
                        <a href="{{ route('join-tenant', $tenant->id) }}" class="tenant-card">
                            <div class="tenant-icon">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <div class="tenant-info">
                                <h4>{{ $tenant->name }}</h4>
                                <p>{{ $tenant->address ?? 'All gaming zones available' }}</p>
                            </div>
                            <div class="tenant-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="no-tenants">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>No gaming zones available at the moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 20px;
}

.auth-card {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 20px;
    width: 100%;
    max-width: 500px;
    overflow: hidden;
}

.auth-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    padding: 40px 30px;
    text-align: center;
}

.auth-header i {
    font-size: 48px;
    color: white;
    margin-bottom: 16px;
}

.auth-header h2 {
    color: white;
    margin: 0 0 8px;
    font-size: 24px;
}

.auth-header p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
}

.auth-body {
    padding: 30px;
}

.tenant-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.tenant-card {
    display: flex;
    align-items: center;
    padding: 16px;
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.tenant-card:hover {
    border-color: #6366f1;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.tenant-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
}

.tenant-icon i {
    font-size: 24px;
    color: white;
}

.tenant-info {
    flex: 1;
}

.tenant-info h4 {
    margin: 0 0 4px;
    color: var(--text-primary);
    font-size: 16px;
}

.tenant-info p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 14px;
}

.tenant-arrow {
    color: var(--text-muted);
}

.no-tenants {
    text-align: center;
    padding: 40px;
}

.no-tenants i {
    font-size: 48px;
    color: var(--text-muted);
    margin-bottom: 16px;
}

.no-tenants p {
    color: var(--text-secondary);
}
</style>
@endsection
