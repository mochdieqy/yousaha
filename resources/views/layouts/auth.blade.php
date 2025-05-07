<!DOCTYPE html>
<html lang="en">
<head>
    @include('shared.meta')
    @include('shared.stylesheet')
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .box-container {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #ffffff;
        }
        h2 {
            font-size: 1.5rem;
        }
        .form-group label {
            font-size: 0.9rem;
        }
        .form-check-label, .btn, a {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="box-container">
        <div class="d-flex justify-content-center">
            <img src="{{ asset('favicon/android-icon-144x144.png') }}" width="100px"/>
        </div>

        <h2 class="text-center">@yield('title')</h2>

        @if(Session::has('message'))
          <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
          </div>
        @endif

        @if($errors->first('message'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('message') }}
            </div>
        @endif

        @yield('content')
    </div>

    @include('shared.script')
</body>
</html>
