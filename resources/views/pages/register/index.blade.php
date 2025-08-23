@extends('layouts.auth')

@section('stylesheet')
@endsection

@section('title', 'Register')

@section('content')
    <!-- General Error Messages -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Success Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Success:</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('auth.sign-up-process') }}">
        {{ csrf_field() }}

        <div class="form-group mb-0">
            <label for="name">Full Name</label>
            <input type="text" name="name" autocomplete="name" class="form-control" id="name" placeholder="Full Name">
        </div>
        @if($errors->first('name'))
          <small class="form-control-feedback text-danger">{{ $errors->first('name') }}</small>
        @endif

        <div class="form-group mb-0 mt-2">
            <label for="email">Email</label>
            <input type="email" name="email" autocomplete="email" class="form-control" id="email" placeholder="Email">
        </div>
        @if($errors->first('email'))
          <small class="form-control-feedback text-danger">{{ $errors->first('email') }}</small>
        @endif

        <div class="form-group mb-0 mt-2">
            <label for="password">Password</label>
            <input type="password" name="password" autocomplete="new-password" class="form-control" id="password" placeholder="Password">
        </div>
        @if($errors->first('password'))
          <small class="text-danger">{{ $errors->first('password') }}</small>
        @endif

        <div class="form-group mb-0 mt-2">
            <label for="confirmation-password">Confirm Password</label>
            <input type="password" name="confirmation_password" class="form-control" id="confirmation-password" placeholder="Confirm Password">
        </div>
        @if($errors->first('confirmation_password'))
          <small class="text-danger">{{ $errors->first('confirmation_password') }}</small>
        @endif

        <div class="form-check icon-check mb-2 mt-2">
            <input class="form-check-input" type="checkbox" name="terms" id="check-terms">
            <label class="form-check-label" for="check-terms">I agree to the <a href="{{ route('auth.sign-in') }}" class="color-highlight">Terms of Service</a></label>
        </div>
        @if($errors->first('terms'))
          <small class="text-danger">{{ $errors->first('terms') }}</small>
        @endif

        {!! NoCaptcha::display() !!}
        @if($errors->first('g-recaptcha-response'))
          <small class="text-danger">{{ $errors->first('g-recaptcha-response') }}</small>
        @endif

        <button type="submit" class="btn btn-dark btn-block mt-4">Register</button>
    </form>
    <p class="text-center mt-2 mb-0">
        <a href="{{ route('auth.sign-in') }}">Already have an account</a>
    </p>
@endsection

@section('script')
{!! NoCaptcha::renderJs() !!}
@endsection
