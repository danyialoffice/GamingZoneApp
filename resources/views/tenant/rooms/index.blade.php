@extends('layouts.app')

@section('title', 'Rooms - Gaming Zone')
@section('page_title', 'Rooms')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-secondary mb-0">Manage your gaming rooms</p>
    <a href="{{ route('tenant.rooms.create') }}" class="btn-primary-custom">
        <i class="fas fa-plus"></i>
        Add Room
    </a>
</div>

<div class="card-custom">
    <div class="card-body-custom p-0">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Hourly Rate</th>
                    <th>PCs</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                <tr>
                    <td><strong>{{ $room->name }}</strong></td>
                    <td style="color: var(--text-secondary);">{{ ucfirst($room->type) }}</td>
                    <td>${{ number_format($room->hourly_rate, 2) }}</td>
                    <td>{{ $room->pcs->count() }}</td>
                    <td>
                        <span class="badge-custom badge-{{ $room->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tenant.rooms.show', $room) }}" class="btn-primary-custom" style="padding: 8px 12px;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('tenant.rooms.edit', $room) }}" class="btn-secondary-custom" style="padding: 8px 12px;">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 48px; color: var(--text-muted);">
                        <i class="fas fa-door-open fa-2x mb-3"></i>
                        <p class="mb-0">No rooms found</p>
                        <a href="{{ route('tenant.rooms.create') }}" class="btn-primary-custom mt-3">
                            Add your first room
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection