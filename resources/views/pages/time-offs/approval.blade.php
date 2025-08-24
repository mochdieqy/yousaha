@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        @if($isCompanyOwner)
                            Company Time Off Approval Queue
                            <small class="text-muted d-block">(Company Owner - All Employees)</small>
                        @else
                            Time Off Approval Queue
                            <small class="text-muted d-block">(Manager - Managed Employees)</small>
                        @endif
                    </h5>
                    <a href="{{ route('time-offs.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Time Offs
                    </a>
                </div>
                <div class="card-body">
                    @if($timeOffs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Reason</th>
                                    <th>Department</th>
                                    @if($isCompanyOwner)
                                        <th>Manager</th>
                                    @endif
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timeOffs as $timeOff)
                                <tr>
                                    <td>
                                        <strong>{{ $timeOff->employee->user->name }}</strong>
                                        <br><small class="text-muted">{{ $timeOff->employee->number }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $timeOff->date->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $timeOff->date->format('l') }}</small>
                                    </td>
                                    <td>
                                        {{ Str::limit($timeOff->reason, 100) }}
                                    </td>
                                    <td>
                                        {{ $timeOff->employee->department->name ?? 'N/A' }}
                                    </td>
                                    @if($isCompanyOwner)
                                        <td>
                                            @if($timeOff->employee->managerUser)
                                                <small class="text-muted">{{ $timeOff->employee->managerUser->name }}</small>
                                            @else
                                                <span class="text-muted">No Manager</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        <a href="{{ route('time-offs.approval-form', $timeOff) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            Review & Approve
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                            <p>
                                @if($isCompanyOwner)
                                    No pending time off requests in the company.
                                @else
                                    No pending time off requests from your managed employees.
                                @endif
                            </p>
                            <a href="{{ route('time-offs.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Time Offs
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
