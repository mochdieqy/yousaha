@extends('layouts.auth')

@section('stylesheet')
@endsection

@section('title', 'Register - Yousaha ERP')

@section('content')
    <form method="POST" action="{{ route('auth.sign-up-process') }}">
        {{ csrf_field() }}

        <div class="form-group mb-0">
            <label for="name">Full Name <span class="text-danger">*</span></label>
            <input type="text" 
                   name="name" 
                   autocomplete="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   id="name" 
                   placeholder="Full Name"
                   value="{{ old('name') }}"
                   required>
        </div>
        @error('name')
            <small class="form-control-feedback text-danger">{{ $message }}</small>
        @enderror

        <div class="form-group mb-0 mt-2">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" 
                   name="email" 
                   autocomplete="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   placeholder="Email"
                   value="{{ old('email') }}"
                   required>
        </div>
        @error('email')
            <small class="form-control-feedback text-danger">{{ $message }}</small>
        @enderror

        <div class="form-group mb-0 mt-2">
            <label for="password">Password <span class="text-danger">*</span></label>
            <input type="password" 
                   name="password" 
                   autocomplete="new-password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   placeholder="Password"
                   required>
            <div class="form-text">Password must be at least 8 characters long.</div>
        </div>
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="form-group mb-0 mt-2">
            <label for="confirmation-password">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" 
                   name="confirmation_password" 
                   class="form-control @error('confirmation_password') is-invalid @enderror" 
                   id="confirmation-password" 
                   placeholder="Confirm Password"
                   required>
        </div>
        @error('confirmation_password')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="form-check icon-check mb-2 mt-2">
            <input class="form-check-input @error('terms') is-invalid @enderror" 
                   type="checkbox" 
                   name="terms" 
                   id="check-terms"
                   required>
            <label class="form-check-label" for="check-terms">
                I agree to the <a href="{{ route('additional-page.terms') }}" class="color-highlight">Terms of Service</a>
            </label>
        </div>
        @error('terms')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        {!! NoCaptcha::display() !!}
        @error('g-recaptcha-response')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <button type="submit" class="btn btn-dark btn-block mt-4">Register</button>
    </form>
    
    <p class="text-center mt-2 mb-0">
        <a href="{{ route('auth.sign-in') }}">Already have an account</a>
    </p>
    
    <p class="text-center mt-2 mb-0">
        <a href="{{ route('auth.resend-verification') }}">Didn't receive verification email?</a>
    </p>
@endsection

@section('script')
{!! NoCaptcha::renderJs() !!}
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Password confirmation validation
    document.getElementById('confirmation-password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;
        
        if (password !== confirmation) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endsection
