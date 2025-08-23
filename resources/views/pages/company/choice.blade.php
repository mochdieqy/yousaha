@extends('layouts.home')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Welcome to Yousaha ERP
                    </h3>
                    <p class="mb-0 mt-2">Choose your role to get started</p>
                </div>
                
                <div class="card-body p-5">
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

                    <div class="text-center mb-4">
                        <p class="text-muted">You're not currently associated with any company. Please select your role:</p>
                    </div>
                    
                    <div class="row">
                        <!-- Business Owner Option -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-2 hover-shadow" style="cursor: pointer;" onclick="window.location.href='{{ route('company.create') }}'">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-crown fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="card-title text-primary">Business Owner</h5>
                                    <p class="card-text text-muted small">
                                        I own or want to start a business and need to manage my company operations.
                                    </p>
                                    <div class="mt-3">
                                        <span class="badge bg-warning text-dark">Create Company</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Employee Option -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-2 hover-shadow" style="cursor: pointer;" onclick="window.location.href='{{ route('company.employee-invitation') }}'">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-user-tie fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title text-info">Employee</h5>
                                    <p class="card-text text-muted small">
                                        I work for a company and need to be invited by my manager or boss.
                                    </p>
                                    <div class="mt-3">
                                        <span class="badge bg-info">Get Invited</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('auth.sign-out') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Information Cards -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                What happens next?
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>Business Owner:</strong> Create your company profile and start managing operations
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>Employee:</strong> Contact your manager to get invited to the company
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li class="mb-2">
                                            <i class="fas fa-shield-alt text-primary me-2"></i>
                                            Secure multi-tenant system
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-users text-primary me-2"></i>
                                            Team collaboration features
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    transition: all 0.3s ease;
}

.card {
    transition: all 0.3s ease;
}
</style>
@endsection
