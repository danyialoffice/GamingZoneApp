@extends('layouts.app')

@section('title', 'Add PC - Gaming Zone')
@section('page_title', 'Add PC')

@section('content')
<div class="breadcrumb-custom">
    <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('tenant.pcs.index') }}">PCs</a>
    <i class="fas fa-chevron-right"></i>
    <span>Add PC</span>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h5 class="card-title-custom">
            <i class="fas fa-desktop me-2" style="color: var(--accent-primary);"></i>
            Add New PC
        </h5>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('tenant.pcs.store') }}" method="POST">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">PC Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" class="form-control-custom @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required placeholder="e.g. PC-01">
                        @error('name')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Room <span style="color: var(--danger);">*</span></label>
                        <select class="form-select-custom @error('room_id') is-invalid @enderror" name="room_id" required>
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Hourly Rate ($) <span style="color: var(--danger);">*</span></label>
                        <input type="number" step="0.01" class="form-control-custom @error('hourly_rate') is-invalid @enderror" 
                               name="hourly_rate" value="{{ old('hourly_rate') }}" required placeholder="0.00">
                        @error('hourly_rate')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Status <span style="color: var(--danger);">*</span></label>
                        <select class="form-select-custom" name="status" required>
                            <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Specifications</label>
                        <textarea class="form-control-custom" name="specs" rows="3" placeholder="CPU, GPU, RAM, etc...">{{ old('specs') }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('tenant.pcs.index') }}" class="btn-secondary-custom">
                    Cancel
                </a>
                <button type="submit" class="btn-primary-custom">
                    <i class="fas fa-save"></i>
                    Save PC
                </button>
            </div>
        </form>
    </div>
</div>
@endsection