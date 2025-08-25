@extends('layouts.auth')

@section('title', 'Forgot Password - Yousaha ERP')

@section('content')
    <form method="POST" action="{{ route('auth.forgot-password-process') }}">
        {{ csrf_field() }}
        
        <div class="form-group mb-0">
            <label for="email">Email</label>
            <input type="email" 
                   name="email" 
                   autocomplete="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   placeholder="Email"
                   value="{{ old('email') }}"
                   required 
                   autofocus>
        </div>
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <button type="submit" class="btn btn-dark btn-block mt-4">Send Reset Link</button>
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
