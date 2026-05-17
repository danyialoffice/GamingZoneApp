@extends('layouts.app')

@section('title', 'PCs - Gaming Zone')
@section('page_title', 'PCs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-secondary mb-0">Manage your gaming PCs</p>
    <a href="{{ route('tenant.pcs.create') }}" class="btn-primary-custom">
        <i class="fas fa-plus"></i>
        Add PC
    </a>
</div>

<div class="card-custom">
    <div class="card-body-custom p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Room</th>
                    <th>Specs</th>
                    <th>Hourly Rate</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pcs as $pc)
                <tr>
                    <td><strong>{{ $pc->name }}</strong></td>
                    <td style="color: var(--text-secondary);">{{ $pc->room->name ?? 'N/A' }}</td>
                    <td style="color: var(--text-secondary); font-size: 12px;">{{ Str::limit($pc->specs, 30) }}</td>
                    <td>${{ number_format($pc->hourly_rate, 2) }}</td>
                    <td>
                        <span class="badge-custom badge-{{ $pc->status === 'available' ? 'success' : ($pc->status === 'in_use' ? 'warning' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $pc->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tenant.pcs.show', $pc) }}" class="btn-primary-custom" style="padding: 8px 12px;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('tenant.pcs.edit', $pc) }}" class="btn-secondary-custom" style="padding: 8px 12px;">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 48px; color: var(--text-muted);">
                        <i class="fas fa-desktop fa-2x mb-3"></i>
                        <p class="mb-0">No PCs found</p>
                        <a href="{{ route('tenant.pcs.create') }}" class="btn-primary-custom mt-3">
                            Add your first PC
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection