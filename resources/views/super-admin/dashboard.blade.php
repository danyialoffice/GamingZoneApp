@extends('layouts.app')

@section('title', 'Super Admin Dashboard - Gaming Zone')
@section('page_title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon primary">
                    <i class="fas fa-building"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Total Tenants</div>
                    <div class="stats-value">{{ $stats['total_tenants'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Active Tenants</div>
                    <div class="stats-value">{{ $stats['active_tenants'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Total Users</div>
                    <div class="stats-value">{{ $stats['total_users'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon warning">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-label">Total Bookings</div>
                    <div class="stats-value">{{ $stats['total_bookings'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-building me-2" style="color: var(--accent-primary);"></i>
                    Recent Tenants
                </h5>
            </div>
            <div class="card-body-custom p-0">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTenants as $tenant)
                        <tr>
                            <td>
                                <a href="{{ route('super-admin.tenants.show', $tenant) }}" style="color: var(--accent-primary); text-decoration: none;">
                                    {{ $tenant->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge-custom badge-{{ $tenant->subscription_plan === 'pro' ? 'primary' : 'secondary' }}">
                                    {{ strtoupper($tenant->subscription_plan) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-custom badge-{{ $tenant->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td style="color: var(--text-secondary);">{{ $tenant->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 32px; color: var(--text-muted);">No tenants found</td>
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
                    Recent Bookings
                </h5>
            </div>
            <div class="card-body-custom p-0">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                        <tr>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td style="color: var(--text-secondary);">{{ $booking->tenant->name ?? 'N/A' }}</td>
                            <td>${{ number_format($booking->total_amount, 2) }}</td>
                            <td>
                                <span class="badge-custom badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 32px; color: var(--text-muted);">No bookings found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
                    <a href="{{ route('super-admin.tenants.create') }}" class="btn-primary-custom">
                        <i class="fas fa-plus"></i>
                        Create New Tenant
                    </a>
                    <a href="{{ route('super-admin.tenants.index') }}" class="btn-secondary-custom">
                        <i class="fas fa-list"></i>
                        View All Tenants
                    </a>
                    <a href="{{ route('home') }}" class="btn-secondary-custom">
                        <i class="fas fa-home"></i>
                        Go to Website
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection