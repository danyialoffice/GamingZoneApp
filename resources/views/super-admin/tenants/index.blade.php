@extends('layouts.app')

@section('title', 'Manage Tenants - Gaming Zone')
@section('page_title', 'Tenants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-secondary mb-0">All gaming zones on the platform</p>
    </div>
    <a href="{{ route('super-admin.tenants.create') }}" class="btn-primary-custom">
        <i class="fas fa-plus"></i>
        Create Tenant
    </a>
</div>

<div class="card-custom">
    <div class="card-body-custom p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Subdomain</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Subscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td><strong>{{ $tenant->name }}</strong></td>
                    <td><code style="background: var(--bg-secondary); padding: 4px 8px; border-radius: 4px;">{{ $tenant->subdomain }}</code></td>
                    <td>
                        <span class="badge-custom badge-{{ $tenant->subscription_plan === 'pro' ? 'primary' : ($tenant->subscription_plan === 'enterprise' ? 'secondary' : 'secondary') }}">
                            {{ strtoupper($tenant->subscription_plan) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-custom badge-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'trial' ? 'info' : 'warning') }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td style="color: var(--text-secondary);">
                        <small>{{ $tenant->subscription_start?->format('M d, Y') ?? 'N/A' }} - {{ $tenant->subscription_end?->format('M d, Y') ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn-primary-custom" style="padding: 8px 12px;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn-secondary-custom" style="padding: 8px 12px;">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 48px; color: var(--text-muted);">
                        <i class="fas fa-building fa-2x mb-3"></i>
                        <p class="mb-0">No tenants found</p>
                        <a href="{{ route('super-admin.tenants.create') }}" class="btn-primary-custom mt-3">
                            Create the first tenant
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection