@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-user-shield me-2"></i>
                    Employee Access Management
                </h2>
                <div>
                    <a href="{{ route('roles.index') }}" class="btn btn-info me-2">
                        <i class="fas fa-eye me-1"></i>
                        View Available Roles
                    </a>
                    <a href="{{ route('employee-roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Assign New Role
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Employee Roles Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Email</th>
                                    <th>Current Roles</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $employee->name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $employee->email }}</td>
                                        <td>
                                            @if($employee->roles->count() > 0)
                                                @foreach($employee->roles as $role)
                                                    <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No roles assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('employee-roles.create') }}?employee_id={{ $employee->id }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Add Role
                                                </a>
                                                @if($employee->roles->count() > 0)
                                                    <form action="{{ route('employee-roles.destroy', $employee->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to remove all roles from this employee?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i>
                                                            Remove All
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <p>No employees found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
