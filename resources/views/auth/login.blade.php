@extends('layouts.app')

@section('title', 'Login - Gaming Zone')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="fas fa-gamepad"></i>
            <h1>Gaming Zone</h1>
            <p>Welcome back! Please login to your account.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control-custom @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
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
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="remember" style="width: 16px; height: 16px;">
                    <span style="color: var(--text-secondary); font-size: 14px;">Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center; padding: 14px;">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; color: var(--text-secondary);">
            Don't have an account? 
            <a href="{{ route('register') }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">Register</a>
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