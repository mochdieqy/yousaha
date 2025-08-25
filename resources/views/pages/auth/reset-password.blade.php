@extends('layouts.auth')

@section('title', 'Reset Password - Yousaha ERP')

@section('content')
    <form method="POST" action="{{ route('auth.reset-password-process', $token) }}">
        {{ csrf_field() }}
        
        <div class="form-group mb-0">
            <label for="password">New Password</label>
            <input type="password" 
                   name="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   placeholder="New Password"
                   required 
                   autofocus>
        </div>
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="form-group mb-0 mt-2">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" 
                   name="password_confirmation" 
                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                   id="password_confirmation" 
                   placeholder="Confirm New Password"
                   required>
        </div>
        @error('password_confirmation')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <button type="submit" class="btn btn-dark btn-block mt-4">Reset Password</button>
    </form>
    
    <p class="text-center mt-2 mb-0">
        <a href="{{ route('auth.sign-in') }}">Back to Login</a>
    </p>
    
    <p class="text-center mt-2 mb-0">
        <a href="{{ route('auth.sign-up') }}">Create Account</a>
    </p>

    <p class="text-center mt-4 mb-0">
        <a href="{{ route('additional-page.about') }}" class="color-highlight">
            About Us
        </a>
        ·
        <a href="{{ route('additional-page.terms') }}" class="color-highlight">
            Terms of Service
        </a>
        ·
        <a href="{{ route('additional-page.privacy') }}" class="color-highlight">
            Privacy Policy
        </a>
    </p>
@endsection

@section('script')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endsection
