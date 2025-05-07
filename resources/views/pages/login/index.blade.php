@extends('layouts.auth')

@section('stylesheet')
@endsection

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ route('auth.sign-in-process') }}">
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
            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
        </div>
        @if($errors->first('password'))
          <small class="text-danger">{{ $errors->first('password') }}</small>
        @endif

        <button type="submit" class="btn btn-dark btn-block mt-4">Login</button>
    </form>
    <p class="text-center mt-2 mb-0">
        <a href="{{ route('auth.sign-up') }}">Buat akun</a>
    </p>

    <p class="text-center mt-4 mb-0">
        <a href="{{ route('additional-page.about') }}" class="color-highlight">
            Tentang Kami
        </a>
        ·
        <a href="{{ route('additional-page.terms') }}" class="color-highlight">
            Syarat Layanan
        </a>
        ·
        <a href="{{ route('additional-page.privacy') }}" class="color-highlight">
            Kebijakan Privasi
        </a>
    </p>
@endsection

@section('script')
@endsection
