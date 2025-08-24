@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-money-check-alt me-2"></i>
                        Payroll Management
                    </h5>
                    @can('payrolls.create')
                    <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Create Payroll
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Basic Salary</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($payroll->period)->format('M Y') }}</strong>
                                    </td>
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
                                        <span class="text-primary fw-bold">
                                            {{ number_format($payroll->basic_salary, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payroll->allowances > 0)
                                            <span class="text-success">+{{ number_format($payroll->allowances, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                        @if($payroll->overtime_pay > 0)
                                            <br><small class="text-info">OT: +{{ number_format($payroll->overtime_pay, 0, ',', '.') }}</small>
                                        @endif
                                        @if($payroll->bonus > 0)
                                            <br><small class="text-warning">Bonus: +{{ number_format($payroll->bonus, 0, ',', '.') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payroll->deductions > 0)
                                            <span class="text-danger">-{{ number_format($payroll->deductions, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold fs-6">
                                            {{ number_format($payroll->net_salary, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payroll->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-success">Processed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($payroll->status === 'pending')
                                                @can('payrolls.edit')
                                                <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('payrolls.delete')
                                                <button type="button" class="btn btn-outline-danger" title="Delete" 
                                                        onclick="confirmDelete('{{ route('payrolls.delete', $payroll) }}', '{{ $payroll->employee->user->name }} - {{ \Carbon\Carbon::parse($payroll->period)->format('M Y') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcan
                                                @can('payrolls.edit')
                                                <form action="{{ route('payrolls.process', $payroll) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Process Payroll">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @else
                                                <span class="text-muted">Processed</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-money-bill-wave fa-2x mb-3"></i>
                                        <p>No payroll records found.</p>
                                        @can('payrolls.create')
                                        <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Create First Payroll
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
                <p>Are you sure you want to delete the payroll record "<strong id="deletePayrollName"></strong>"?</p>
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
