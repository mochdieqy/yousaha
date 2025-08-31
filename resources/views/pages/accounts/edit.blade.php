@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Account
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
                        <li class="breadcrumb-item active">Edit Account</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Accounts
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ Auth::user()->currentCompany->name }}
                    <br>
                    <small class="text-muted">Account will be updated in this company's chart of accounts</small>
                </div>
            </div>
        </div>

        <!-- Account Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Account Information
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('accounts.update', $account) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>
                                    Account Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $account->code) }}" 
                                       placeholder="Enter account code (e.g., 1000, 1100)"
                                       required>
                                <div class="form-text">Enter a unique account code</div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">
                                    <i class="fas fa-tag me-1"></i>
                                    Account Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Select Account Type</option>
                                    @foreach($accountTypes as $type => $label)
                                        <option value="{{ $type }}" {{ old('type', $account->type) == $type ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-font me-1"></i>
                            Account Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $account->name) }}" 
                               placeholder="Enter descriptive account name"
                               required>
                        <div class="form-text">Enter a descriptive account name</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($account->isCriticalAccount())
                        <div class="alert alert-warning border-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This is a critical system account used in sales and purchase orders. 
                            Changes to this account may affect system functionality.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
