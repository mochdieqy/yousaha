@extends('layouts.home')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-info text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Employee Access Required
                    </h3>
                    <p class="mb-0 mt-2">You need to be invited by your company</p>
                </div>
                
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-envelope-open-text fa-4x text-info mb-3"></i>
                        <h4 class="text-primary">Waiting for Invitation</h4>
                        <p class="text-muted">
                            To access Yousaha ERP, you need to be invited by your manager or company owner.
                        </p>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            What you need to do:
                        </h6>
                        <ol class="mb-0 text-start">
                            <li>Contact your manager or company owner</li>
                            <li>Ask them to invite you to the company in Yousaha</li>
                            <li>They will need your email address: <strong>{{ Auth::user()->email }}</strong></li>
                            <li>Once invited, you'll receive an email notification</li>
                        </ol>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-phone fa-2x text-info mb-2"></i>
                                    <h6>Contact Your Manager</h6>
                                    <p class="small text-muted mb-0">
                                        Reach out directly to your supervisor
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope fa-2x text-info mb-2"></i>
                                    <h6>Email Request</h6>
                                    <p class="small text-muted mb-0">
                                        Send a formal request via email
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('company.choice') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Choice
                        </a>
                        <a href="{{ route('auth.sign-out') }}" class="btn btn-secondary">
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
                                <i class="fas fa-question-circle me-2"></i>
                                Frequently Asked Questions
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6 class="text-info">Why do I need an invitation?</h6>
                                        <p class="small text-muted mb-0">
                                            Yousaha is a multi-tenant ERP system that ensures data security and isolation between companies.
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="text-info">What information should I provide?</h6>
                                        <p class="small text-muted mb-0">
                                            Your full name, email address, and the role/position you'll have in the company.
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6 class="text-info">How long does it take?</h6>
                                        <p class="small text-muted mb-0">
                                            Usually within 24 hours, depending on your manager's response time.
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="text-info">What happens after invitation?</h6>
                                        <p class="small text-muted mb-0">
                                            You'll receive an email with login instructions and access to your company's ERP system.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Support -->
            <div class="card border-0 bg-warning mt-4">
                <div class="card-body text-center">
                    <h6 class="text-dark mb-2">
                        <i class="fas fa-headset me-2"></i>
                        Need Help?
                    </h6>
                    <p class="text-dark mb-2">
                        If you're having trouble getting access, contact our support team.
                    </p>
                    <a href="mailto:support@yousaha.com" class="btn btn-dark btn-sm">
                        <i class="fas fa-envelope me-2"></i>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
