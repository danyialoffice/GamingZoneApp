@extends('layouts.app')

@section('title', 'Register - Gaming Zone')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="fas fa-gamepad"></i>
            <h1>Gaming Zone</h1>
            <p>Create your account and start gaming!</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control-custom @error('name') is-invalid @enderror" 
                       name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your name">
                @error('name')
                    <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control-custom @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                @error('email')
                    <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" class="form-control-custom @error('password') is-invalid @enderror" 
                       name="password" required placeholder="Enter your password">
                @error('password')
                    <div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control-custom" 
                       name="password_confirmation" required placeholder="Confirm your password">
            </div>

            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 14px;">
                <i class="fas fa-user-plus"></i>
                Register
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; color: var(--text-secondary);">
            Already have an account? 
            <a href="{{ route('login') }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">Login</a>
        </div>

        <div style="text-align: center; margin-top: 16px;">
            <a href="{{ route('home') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 14px;">
                <i class="fas fa-home me-2"></i>
                Back to Website
            </a>
        </div>
    </div>
</div>
@endsection