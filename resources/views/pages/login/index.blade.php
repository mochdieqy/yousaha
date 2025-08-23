@extends('layouts.auth')

@section('stylesheet')
@endsection

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ route('auth.sign-in-process') }}">
        {{ csrf_field() }}
        <div class="form-group mb-0">
            <label for="email">Email</label>
            <input type="email" name="email" autocomplete="email" class="form-control" id="email" placeholder="Email">
        </div>
        @if($errors->first('email'))
          <small class="text-danger">{{ $errors->first('email') }}</small>
        @endif

        <div class="form-group mb-0 mt-2">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
        </div>
        @if($errors->first('password'))
          <small class="text-danger">{{ $errors->first('password') }}</small>
        @endif

        <button type="submit" class="btn btn-dark btn-block mt-4">Login</button>
    </form>
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
@endsection
