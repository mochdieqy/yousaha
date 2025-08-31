@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Edit Department: {{ $department->name }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Departments
            </a>
        </div>

        <!-- Company Info -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-building me-3 fa-lg"></i>
                <div>
                    <strong>Company:</strong> {{ $company->name }}
                    <br>
                    <small class="text-muted">Department belongs to this company's structure</small>
                </div>
            </div>
        </div>

        <!-- Department Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Department Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('departments.update', $department) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-tag me-1"></i>
                                        Department Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $department->name) }}" 
                                           placeholder="Enter department name"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="code" class="form-label">
                                        <i class="fas fa-code me-1"></i>
                                        Department Code <span class="text-muted">(Optional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $department->code) }}" 
                                           placeholder="e.g., IT, HR, FIN">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="manager_id" class="form-label">
                                        <i class="fas fa-user-tie me-1"></i>
                                        Department Manager <span class="text-muted">(Optional)</span>
                                    </label>
                                    <select class="form-select @error('manager_id') is-invalid @enderror" 
                                            id="manager_id" 
                                            name="manager_id">
                                        <option value="">Select Manager</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('manager_id', $department->manager_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="location" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        Location <span class="text-muted">(Optional)</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('location') is-invalid @enderror" 
                                           id="location" 
                                           name="location" 
                                           value="{{ old('location', $department->location) }}" 
                                           placeholder="e.g., Floor 3, Building A">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="parent_id" class="form-label">
                                        <i class="fas fa-sitemap me-1"></i>
                                        Parent Department <span class="text-muted">(Optional)</span>
                                    </label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" 
                                            id="parent_id" 
                                            name="parent_id">
                                        <option value="">No Parent Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('parent_id', $department->parent_id) == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>
                                    Description <span class="text-muted">(Optional)</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Brief description of the department's function and responsibilities">{{ old('description', $department->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Help Information -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        Tips
                                    </h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i>
                                                Department names should be clear and descriptive
                                            </small>
                                        </li>
                                        <li class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i>
                                                Codes help with quick identification
                                            </small>
                                        </li>
                                        <li class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i>
                                                Parent departments create organizational hierarchy
                                            </small>
                                        </li>
                                        <li class="mb-0">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i>
                                                Managers can be assigned later
                                            </small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
