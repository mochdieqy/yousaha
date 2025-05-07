@extends('layouts.auth')

@section('stylesheet')
@endsection

@section('title', 'Register')

@section('content')
    <form method="POST" action="{{ route('auth.sign-up-process') }}">
        {{ csrf_field() }}

        <div class="form-group mb-0">
            <label for="username">Username</label>
            <input type="username" name="username" autocomplete="username" class="form-control" id="username" placeholder="Username">
        </div>
        @if($errors->first('username'))
          <small class="text-danger">{{ $errors->first('username') }}</small>
        @endif

        <div class="form-group mb-0 mt-2">
            <label for="password">Password</label>
            <input type="password" name="password" autocomplete="new-password" class="form-control" id="password" placeholder="Password">
        </div>
        @if($errors->first('password'))
          <small class="text-danger">{{ $errors->first('password') }}</small>
        @endif

        <div class="form-group mb-0 mt-2">
            <label for="confirmation-password">Konfirmasi Password</label>
            <input type="password" name="confirmation_password" class="form-control" id="confirmation-password" placeholder="Konfirmasi Password">
        </div>
        @if($errors->first('confirmation_password'))
          <small class="text-danger">{{ $errors->first('confirmation_password') }}</small>
        @endif

        <div class="form-check icon-check mb-2 mt-2">
            <input class="form-check-input" type="checkbox" name="terms" id="check-terms">
            <label class="form-check-label" for="check-terms">Setuju dengan ketentuan <a href="{{ route('auth.sign-in') }}" class="color-highlight">Syarat Layanan</a></label>
            <i class="icon-check-1 fa fa-square color-gray-dark font-16"></i>
            <i class="icon-check-2 fa fa-check-square font-16 color-highlight"></i>
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
        <a href="{{ route('auth.sign-in') }}">Sudah punya akun</a>
    </p>
@endsection

@section('script')
{!! NoCaptcha::renderJs() !!}
@endsection
