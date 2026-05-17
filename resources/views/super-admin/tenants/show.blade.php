@extends('layouts.app')

@section('title', $tenant->name . ' - Tenant Details')
@section('page_title', $tenant->name)

@section('content')
<div class="breadcrumb-custom">
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.tenants.index') }}">Tenants</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $tenant->name }}</span>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-building me-2" style="color: var(--accent-primary);"></i>
                    Tenant Information
                </h5>
            </div>
            <div class="card-body-custom">
                <table style="width: 100%;">
                    <tr>
                        <td style="color: var(--text-secondary); padding: 8px 0; width: 40%;">Name</td>
                        <td style="font-weight: 500;">{{ $tenant->name }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary); padding: 8px 0;">Email</td>
                        <td>{{ $tenant->email }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary); padding: 8px 0;">Phone</td>
                        <td>{{ $tenant->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary); padding: 8px 0;">Address</td>
                        <td>{{ $tenant->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary); padding: 8px 0;">Subdomain</td>
                        <td><code style="background: var(--bg-secondary); padding: 4px 8px; border-radius: 4px;">{{ $tenant->subdomain }}</code></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary); padding: 8px 0;">Plan</td>
                        <td>
                            <span class="badge-custom badge-{{ $tenant->subscription_plan === 'pro' ? 'primary' : 'secondary' }}">
                                {{ strtoupper($tenant->subscription_plan) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary); padding: 8px 0;">Status</td>
                        <td>
                            <span class="badge-custom badge-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'trial' ? 'info' : 'warning') }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-body-custom" style="border-top: 1px solid var(--border-color);">
                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn-primary-custom">
                    <i class="fas fa-edit"></i>
                    Edit Tenant
                </a>
            </div>
        </div>

        <div class="card-custom mt-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-chart-bar me-2" style="color: var(--accent-primary);"></i>
                    Plan Limits
                </h5>
            </div>
            <div class="card-body-custom">
                <div class="d-flex justify-content-between mb-2">
                    <span style="color: var(--text-secondary);">Rooms</span>
                    <strong>{{ $tenant->max_rooms == -1 ? 'Unlimited' : $tenant->max_rooms }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color: var(--text-secondary);">PCs</span>
                    <strong>{{ $tenant->max_pcs == -1 ? 'Unlimited' : $tenant->max_pcs }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color: var(--text-secondary);">Staff</span>
                    <strong>{{ $tenant->max_staff == -1 ? 'Unlimited' : $tenant->max_staff }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-icon primary mx-auto mb-2">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="stats-value">{{ $tenant->rooms->count() }}</div>
                    <div class="stats-label">Rooms</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-icon success mx-auto mb-2">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="stats-value">{{ $tenant->pcs->count() }}</div>
                    <div class="stats-label">PCs</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-icon info mx-auto mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-value">{{ $tenant->users->count() }}</div>
                    <div class="stats-label">Users</div>
                </div>
            </div>
        </div>

        <div class="card-custom mb-4">
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
                            <th>PC</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenant->bookings as $booking)
                        <tr>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td>{{ $booking->pc->name ?? 'N/A' }}</td>
                            <td style="color: var(--text-secondary);">{{ $booking->start_time->format('M d, Y H:i') }}</td>
                            <td>${{ number_format($booking->total_amount, 2) }}</td>
                            <td>
                                <span class="badge-custom badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 32px; color: var(--text-muted);">No bookings found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="fas fa-users me-2" style="color: var(--accent-primary);"></i>
                    Tenant Users
                </h5>
            </div>
            <div class="card-body-custom p-0">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenant->users as $tenantUser)
                        <tr>
                            <td>{{ $tenantUser->user->name ?? 'N/A' }}</td>
                            <td style="color: var(--text-secondary);">{{ $tenantUser->user->email ?? 'N/A' }}</td>
                            <td>{{ $tenantUser->role->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-custom badge-{{ $tenantUser->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($tenantUser->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 32px; color: var(--text-muted);">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection