@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Employee Payroll Information Management
                    </h5>
                    @can('payrolls.create')
                    <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Add Employee Payroll Info
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This system manages employee payroll information (bank details, tax numbers, insurance) for payroll setup purposes. 
                        It does NOT handle salary calculations or automatic payments.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Bank</th>
                                    <th>Account Number</th>
                                    <th>Tax Number</th>
                                    <th>Insurance Numbers</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                <tr>
                                    <td>
                                        <strong>{{ $payroll->employee->user->name }}</strong>
                                        <br><small class="text-muted">{{ $payroll->employee->number }}</small>
                                    </td>
                                    <td>
                                        @if($payroll->employee->department)
                                            <span class="badge bg-info">{{ $payroll->employee->department->name }}</span>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            {{ $payroll->payment_account_bank }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="text-dark">{{ $payroll->payment_account_number }}</code>
                                    </td>
                                    <td>
                                        @if($payroll->tax_number)
                                            <span class="badge bg-success">{{ $payroll->tax_number }}</span>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($payroll->employment_insurance_number)
                                                <div class="mb-1">
                                                    <span class="badge bg-info me-1">Employment</span>
                                                    {{ $payroll->employment_insurance_number }}
                                                </div>
                                            @endif
                                            @if($payroll->health_insurance_number)
                                                <div>
                                                    <span class="badge bg-warning me-1">Health</span>
                                                    {{ $payroll->health_insurance_number }}
                                                </div>
                                            @endif
                                            @if(!$payroll->employment_insurance_number && !$payroll->health_insurance_number)
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @can('payrolls.view')
                                            <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('payrolls.edit')
                                            <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('payrolls.delete')
                                            <button type="button" class="btn btn-outline-danger" title="Delete" 
                                                    onclick="confirmDelete('{{ route('payrolls.delete', $payroll) }}', '{{ $payroll->employee->user->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-user-tie fa-2x mb-3"></i>
                                        <p>No employee payroll information found.</p>
                                        @can('payrolls.create')
                                        <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Add First Employee Payroll Info
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
                <p>Are you sure you want to delete the payroll information for "<strong id="deletePayrollName"></strong>"?</p>
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
function confirmDelete(deleteUrl, payrollName) {
    document.getElementById('deletePayrollName').textContent = payrollName;
    document.getElementById('deleteForm').action = deleteUrl;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
