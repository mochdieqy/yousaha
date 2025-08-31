@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-eye text-primary me-2"></i>
                    Receipt Details
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">Receipts</a></li>
                        <li class="breadcrumb-item active">Receipt #{{ $receipt->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @can('receipts.edit')
                @if(!in_array($receipt->status, ['done', 'cancel']))
                <a href="{{ route('receipts.edit', $receipt) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Edit Receipt
                </a>
                @endif
                @endcan
                <a href="{{ route('receipts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Receipts
                </a>
            </div>
        </div>

        <!-- Receipt Information -->
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Receipt Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Receipt ID</label>
                                    <p class="mb-0">#{{ $receipt->id }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Reference</label>
                                    <p class="mb-0">{{ $receipt->reference ?: 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <div>
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-secondary',
                                                'waiting' => 'bg-warning',
                                                'ready' => 'bg-info',
                                                'done' => 'bg-success',
                                                'cancel' => 'bg-danger'
                                            ];
                                            $statusColor = $statusColors[$receipt->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $statusColor }} text-white fs-6">
                                            {{ ucfirst($receipt->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Supplier</label>
                                    <p class="mb-0">{{ $receipt->supplier->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Warehouse</label>
                                    <p class="mb-0">{{ $receipt->warehouse->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Scheduled Date</label>
                                    <p class="mb-0">{{ $receipt->scheduled_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created</label>
                                    <p class="mb-0">{{ $receipt->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="mb-0">{{ $receipt->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products List -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>
                            Products ({{ $receipt->productLines->count() }} items)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Product</th>
                                        <th class="border-0">SKU</th>
                                        <th class="border-0 text-end">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipt->productLines as $productLine)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-box text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $productLine->product->name }}</h6>
                                                    @if($productLine->product->reference)
                                                        <small class="text-muted">{{ $productLine->product->reference }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $productLine->product->sku }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold">{{ number_format($productLine->quantity, 0) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2" class="fw-bold">Total Quantity</td>
                                        <td class="text-end fw-bold">{{ number_format($receipt->productLines->sum('quantity'), 0) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Status History -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Status History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($receipt->statusLogs->sortBy('changed_at') as $statusLog)
                            <div class="timeline-item mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="timeline-marker me-3">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-secondary',
                                                'waiting' => 'bg-warning',
                                                'ready' => 'bg-info',
                                                'done' => 'bg-success',
                                                'cancel' => 'bg-danger'
                                            ];
                                            $statusColor = $statusColors[$statusLog->status] ?? 'bg-secondary';
                                        @endphp
                                        <div class="rounded-circle {{ $statusColor }}" style="width: 12px; height: 12px;"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ ucfirst($statusLog->status) }}</div>
                                        <small class="text-muted">{{ $statusLog->changed_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if(!in_array($receipt->status, ['done', 'cancel']))
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        @can('receipts.edit')
                        <div class="d-grid gap-2">
                            @if($receipt->status === 'draft')
                            <form action="{{ route('receipts.update-status', $receipt) }}" method="POST" class="d-grid">
                                @csrf
                                <input type="hidden" name="status" value="waiting">
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-clock me-2"></i>
                                    Mark as Waiting
                                </button>
                            </form>
                            @elseif($receipt->status === 'waiting')
                            <form action="{{ route('receipts.update-status', $receipt) }}" method="POST" class="d-grid">
                                @csrf
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="fas fa-check me-2"></i>
                                    Mark as Ready
                                </button>
                            </form>
                            @elseif($receipt->status === 'ready')
                            <form action="{{ route('receipts.update-status', $receipt) }}" method="POST" class="d-grid">
                                @csrf
                                <input type="hidden" name="status" value="done">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-truck me-2"></i>
                                    Complete Receipt
                                </button>
                            </form>
                            @endif
                            
                            @if($receipt->status !== 'cancel')
                            <form action="{{ route('receipts.update-status', $receipt) }}" method="POST" class="d-grid">
                                @csrf
                                <input type="hidden" name="status" value="cancel">
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to cancel this receipt?')">
                                    <i class="fas fa-times me-2"></i>
                                    Cancel Receipt
                                </button>
                            </form>
                            @endif
                        </div>
                        @endcan
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Auto-hide success/error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });
});
</script>
@endsection
