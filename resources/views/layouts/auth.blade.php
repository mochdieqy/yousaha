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

        <!-- Global Error Messages -->
        @if(session('error'))
        <div class="row mb-3">
            <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            </div>
        </div>
        @endif

        <!-- Global Success Messages -->
        @if(session('success'))
        <div class="row mb-3">
            <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            </div>
        </div>
        @endif

        <!-- Global Validation Errors -->
        @if($errors->any())
        <div class="row mb-3">
            <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            </div>
        </div>
        @endif

        @yield('content')
    </div>

    @include('shared.script')
</body>
</html>
