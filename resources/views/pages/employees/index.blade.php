@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Employee Management
                    </h5>
                    @can('employees.create')
                    <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Add Employee
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Employee #</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Level</th>
                                    <th>Join Date</th>
                                    <th>Manager</th>
                                    <th>Work Arrangement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $employee->number }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $employee->user->name }}</strong>
                                        <br><small class="text-muted">{{ $employee->user->email }}</small>
                                    </td>
                                    <td>
                                        @if($employee->department)
                                            <span class="badge bg-info">{{ $employee->department->name }}</span>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $employee->position }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $employee->level }}</span>
                                    </td>
                                    <td>
                                        {{ $employee->join_date->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $employee->years_of_service }} years</small>
                                    </td>
                                    <td>
                                        @if($employee->managerUser)
                                            {{ $employee->managerUser->name }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->work_arrangement === 'WFO')
                                            <span class="badge bg-success">WFO</span>
                                        @elseif($employee->work_arrangement === 'WFH')
                                            <span class="badge bg-warning">WFH</span>
                                        @else
                                            <span class="badge bg-info">WFA</span>
                                        @endif
                                        <br><small class="text-muted">{{ $employee->work_location }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('employees.edit')
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('employees.delete')
                                            <button type="button" class="btn btn-outline-danger" title="Delete" 
                                                    onclick="confirmDelete('{{ route('employees.delete', $employee) }}', '{{ $employee->user->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-2x mb-3"></i>
                                        <p>No employees found.</p>
                                        @can('employees.create')
                                        <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Add First Employee
                                        </a>
                                        @endcan
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the employee "<strong id="deleteEmployeeName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function confirmDelete(deleteUrl, employeeName) {
    document.getElementById('deleteEmployeeName').textContent = employeeName;
    document.getElementById('deleteForm').action = deleteUrl;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
