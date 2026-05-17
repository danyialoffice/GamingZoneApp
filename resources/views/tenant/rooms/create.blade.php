@extends('layouts.app')

@section('title', 'Add Room - Gaming Zone')
@section('page_title', 'Add Room')

@section('content')
<div class="breadcrumb-custom">
    <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('tenant.rooms.index') }}">Rooms</a>
    <i class="fas fa-chevron-right"></i>
    <span>Add Room</span>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h5 class="card-title-custom">
            <i class="fas fa-plus-circle me-2" style="color: var(--accent-primary);"></i>
            Add New Room
        </h5>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('tenant.rooms.store') }}" method="POST">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Room Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" class="form-control-custom @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required placeholder="e.g. Gaming Arena 1">
                        @error('name')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Type <span style="color: var(--danger);">*</span></label>
                        <select class="form-select-custom @error('type') is-invalid @enderror" name="type" required>
                            <option value="">Select Type</option>
                            <option value="private" {{ old('type') === 'private' ? 'selected' : '' }}>Private Room</option>
                            <option value="public" {{ old('type') === 'public' ? 'selected' : '' }}>Public Area</option>
                            <option value="vip" {{ old('type') === 'vip' ? 'selected' : '' }}>VIP Room</option>
                        </select>
                        @error('type')
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
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control-custom" name="description" rows="3" placeholder="Room description...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('tenant.rooms.index') }}" class="btn-secondary-custom">
                    Cancel
                </a>
                <button type="submit" class="btn-primary-custom">
                    <i class="fas fa-save"></i>
                    Save Room
                </button>
            </div>
        </form>
    </div>
</div>
@endsection