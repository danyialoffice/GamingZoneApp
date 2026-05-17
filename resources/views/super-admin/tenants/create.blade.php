@extends('layouts.app')

@section('title', 'Create Tenant - Gaming Zone')
@section('page_title', 'Create Tenant')

@section('content')
<div class="breadcrumb-custom">
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.tenants.index') }}">Tenants</a>
    <i class="fas fa-chevron-right"></i>
    <span>Create</span>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <h5 class="card-title-custom">
            <i class="fas fa-building me-2" style="color: var(--accent-primary);"></i>
            Create New Tenant
        </h5>
    </div>
    <div class="card-body-custom">
        <form action="{{ route('super-admin.tenants.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <h6 style="color: var(--text-secondary); border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 16px;">Tenant Information</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tenant Name <span style="color: var(--danger);">*</span></label>
                            <input type="text" class="form-control-custom @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required>
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
                                <option value="">Select Plan</option>
                                <option value="basic" {{ old('subscription_plan') === 'basic' ? 'selected' : '' }}>Basic (2 rooms, 10 PCs)</option>
                                <option value="pro" {{ old('subscription_plan') === 'pro' ? 'selected' : '' }}>Pro (10 rooms, 50 PCs)</option>
                                <option value="enterprise" {{ old('subscription_plan') === 'enterprise' ? 'selected' : '' }}>Enterprise (Unlimited)</option>
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
                                   name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control-custom" name="phone" value="{{ old('phone') }}">
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <textarea class="form-control-custom" name="address" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <h6 style="color: var(--text-secondary); border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 16px;">Tenant Admin Account</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Admin Name <span style="color: var(--danger);">*</span></label>
                            <input type="text" class="form-control-custom @error('admin_name') is-invalid @enderror" 
                                   name="admin_name" value="{{ old('admin_name') }}" required>
                            @error('admin_name')
                                <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Admin Email <span style="color: var(--danger);">*</span></label>
                            <input type="email" class="form-control-custom @error('admin_email') is-invalid @enderror" 
                                   name="admin_email" value="{{ old('admin_email') }}" required>
                            @error('admin_email')
                                <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Password <span style="color: var(--danger);">*</span></label>
                            <input type="password" class="form-control-custom @error('admin_password') is-invalid @enderror" 
                                   name="admin_password" required>
                            @error('admin_password')
                                <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Confirm Password <span style="color: var(--danger);">*</span></label>
                            <input type="password" class="form-control-custom" 
                                   name="admin_password_confirmation" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('super-admin.tenants.index') }}" class="btn-secondary-custom">
                    Cancel
                </a>
                <button type="submit" class="btn-primary-custom">
                    <i class="fas fa-save"></i>
                    Create Tenant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection