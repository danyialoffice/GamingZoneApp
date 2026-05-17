@extends('layouts.app')

@section('title', 'Edit Tenant - Gaming Zone')
@section('page_title', 'Edit ' . $tenant->name)

@section('content')
<div class="breadcrumb-custom">
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.tenants.index') }}">Tenants</a>
    <i class="fas fa-chevron-right"></i>
    <span>Edit {{ $tenant->name }}</span>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h5 class="card-title-custom">
            <i class="fas fa-edit me-2" style="color: var(--accent-primary);"></i>
            Edit Tenant: {{ $tenant->name }}
        </h5>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('super-admin.tenants.update', $tenant) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tenant Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" class="form-control-custom @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $tenant->name) }}" required>
                        @error('name')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Subscription Plan <span style="color: var(--danger);">*</span></label>
                        <select class="form-select-custom @error('subscription_plan') is-invalid @enderror" 
                                name="subscription_plan" required>
                            <option value="basic" {{ old('subscription_plan', $tenant->subscription_plan) === 'basic' ? 'selected' : '' }}>Basic (2 rooms, 10 PCs)</option>
                            <option value="pro" {{ old('subscription_plan', $tenant->subscription_plan) === 'pro' ? 'selected' : '' }}>Pro (10 rooms, 50 PCs)</option>
                            <option value="enterprise" {{ old('subscription_plan', $tenant->subscription_plan) === 'enterprise' ? 'selected' : '' }}>Enterprise (Unlimited)</option>
                        </select>
                        @error('subscription_plan')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Email <span style="color: var(--danger);">*</span></label>
                        <input type="email" class="form-control-custom @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', $tenant->email) }}" required>
                        @error('email')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control-custom" name="phone" value="{{ old('phone', $tenant->phone) }}">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Status <span style="color: var(--danger);">*</span></label>
                        <select class="form-select-custom @error('status') is-invalid @enderror" 
                                name="status" required>
                            <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="trial" {{ old('status', $tenant->status) === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea class="form-control-custom" name="address" rows="2">{{ old('address', $tenant->address) }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn-secondary-custom">
                    Cancel
                </a>
                <button type="submit" class="btn-primary-custom">
                    <i class="fas fa-save"></i>
                    Update Tenant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection