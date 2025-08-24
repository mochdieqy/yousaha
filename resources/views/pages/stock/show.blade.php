@extends('layouts.home')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye text-info me-2"></i>
            Stock Details
        </h1>
        <div class="d-flex gap-2">
            @can('stocks.edit')
            <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i>
                Edit Stock
            </a>
            @endcan
            <a href="{{ route('stocks.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Stock List
            </a>
        </div>
    </div>



    <!-- Stock Overview Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Stock Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Product</label>
                            <div class="form-control-plaintext">
                                <strong>{{ $stock->product->code }}</strong><br>
                                <span class="text-muted">{{ $stock->product->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Warehouse</label>
                            <div class="form-control-plaintext">
                                <span class="badge bg-info">{{ $stock->warehouse->code }}</span><br>
                                <span class="text-muted">{{ $stock->warehouse->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Total Quantity</label>
                            <div class="form-control-plaintext">
                                <span class="h5 text-primary">{{ number_format($stock->quantity_total) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Saleable Quantity</label>
                            <div class="form-control-plaintext">
                                <span class="h5 {{ $stock->quantity_saleable <= 10 ? 'text-warning' : 'text-success' }}">
                                    {{ number_format($stock->quantity_saleable) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Reserved Quantity</label>
                            <div class="form-control-plaintext">
                                <span class="h5 text-muted">{{ number_format($stock->quantity_reserve) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Incoming Quantity</label>
                            <div class="form-control-plaintext">
                                <span class="h5 text-info">{{ number_format($stock->quantity_incoming) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stock Status</label>
                            <div class="form-control-plaintext">
                                @if($stock->isOutOfStock())
                                    <span class="badge bg-danger fs-6">Out of Stock</span>
                                @elseif($stock->isLowStock())
                                    <span class="badge bg-warning text-dark fs-6">Low Stock</span>
                                @else
                                    <span class="badge bg-success fs-6">Normal</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <div class="form-control-plaintext">
                                <span class="text-muted">{{ $stock->updated_at->format('M d, Y H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Summary Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('stocks.edit')
                        <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Adjust Stock
                        </a>
                        @endcan
                        
                        @if($stock->quantity_saleable > 0)
                        <button type="button" class="btn btn-success" disabled>
                            <i class="fas fa-truck me-2"></i>
                            Create Delivery
                        </button>
                        @else
                        <button type="button" class="btn btn-secondary" disabled>
                            <i class="fas fa-truck me-2"></i>
                            No Stock Available
                        </button>
                        @endif

                        <button type="button" class="btn btn-info" disabled>
                            <i class="fas fa-receipt me-2"></i>
                            Create Receipt
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stock Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ $stock->details->count() }}</h4>
                                <small class="text-muted">Stock Details</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info mb-0">{{ $stock->histories->count() }}</h4>
                            <small class="text-muted">History Records</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Details Card -->
    @if($stock->details->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                Stock Details
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Quantity</th>
                            <th>Code</th>
                            <th>Cost</th>
                            <th>Reference</th>
                            <th>Expiration Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stock->details as $detail)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ number_format($detail->quantity) }}</span>
                            </td>
                            <td>
                                @if($detail->code)
                                    <span class="badge bg-secondary">{{ $detail->code }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($detail->cost)
                                    <span class="text-success">{{ number_format($detail->cost, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($detail->reference)
                                    <small>{{ $detail->reference }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($detail->expiration_date)
                                    <span class="{{ $detail->isExpired() ? 'text-danger' : ($detail->isExpiringSoon() ? 'text-warning' : 'text-success') }}">
                                        {{ $detail->expiration_date->format('M d, Y') }}
                                        @if($detail->isExpired())
                                            <br><small class="badge bg-danger">Expired</small>
                                        @elseif($detail->isExpiringSoon())
                                            <br><small class="badge bg-warning text-dark">Expiring Soon</small>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($detail->cost)
                                    <span class="text-success">
                                        Total: {{ number_format($detail->total_value, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Stock History Card -->
    @if($stock->histories->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>
                Stock History
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Total Qty Change</th>
                            <th>Saleable Qty Change</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stock->histories->sortByDesc('date') as $history)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $history->date->format('M d, Y H:i') }}
                                </small>
                            </td>
                            <td>
                                @switch($history->type)
                                    @case('initial')
                                        <span class="badge bg-primary">Initial</span>
                                        @break
                                    @case('adjustment')
                                        <span class="badge bg-warning text-dark">Adjustment</span>
                                        @break
                                    @case('receipt')
                                        <span class="badge bg-success">Receipt</span>
                                        @break
                                    @case('delivery')
                                        <span class="badge bg-info">Delivery</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($history->type) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($history->isIncrease())
                                    <span class="text-success">+{{ number_format($history->total_quantity_change) }}</span>
                                @elseif($history->isDecrease())
                                    <span class="text-danger">{{ number_format($history->total_quantity_change) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @if($history->saleable_quantity_change > 0)
                                    <span class="text-success">+{{ number_format($history->saleable_quantity_change) }}</span>
                                @elseif($history->saleable_quantity_change < 0)
                                    <span class="text-danger">{{ number_format($history->saleable_quantity_change) }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $history->reference }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
