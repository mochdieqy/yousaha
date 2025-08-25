@extends('layouts.home')

@section('title', 'Edit AI Evaluation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Edit AI Evaluation</h4>
                <div class="page-title-right">
                    <a href="{{ route('ai-evaluation.show', $evaluation) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Evaluation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('ai-evaluation.update', $evaluation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Evaluation Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $evaluation->title) }}" 
                                           placeholder="Enter evaluation title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Evaluation Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $key => $name)
                                            <option value="{{ $key }}" {{ old('category', $evaluation->category) == $key ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_start" class="form-label">Period Start</label>
                                    <input type="date" 
                                           class="form-control @error('period_start') is-invalid @enderror" 
                                           id="period_start" 
                                           name="period_start" 
                                           value="{{ old('period_start', $evaluation->period_start ? $evaluation->period_start->format('Y-m-d') : '') }}">
                                    <div class="form-text">Leave empty to analyze all data</div>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="period_end" class="form-label">Period End</label>
                                    <input type="date" 
                                           class="form-control @error('period_end') is-invalid @enderror" 
                                           id="period_end" 
                                           name="period_end" 
                                           value="{{ old('period_end', $evaluation->period_end ? $evaluation->period_end->format('Y-m-d') : '') }}">
                                    <div class="form-text">Leave empty to analyze all data</div>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle"></i> What will be analyzed?
                                    </h6>
                                    <div id="category-description">
                                        <p class="mb-0">Select a category to see what data will be analyzed.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-exclamation-triangle"></i> Important Note
                                    </h6>
                                    <p class="mb-0">
                                        Editing this evaluation will regenerate the AI analysis with the new parameters. 
                                        The previous AI-generated content will be replaced with new insights based on the updated criteria.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('ai-evaluation.show', $evaluation) }}" class="btn btn-secondary me-2">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update & Regenerate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    const categoryDescriptions = {
        'sales_order': 'This will analyze your sales order data including total orders, revenue, completion rates, customer performance, and monthly trends to provide insights on sales performance and customer behavior.',
        'purchase_order': 'This will analyze your purchase order data including total orders, amounts, completion rates, supplier performance, and monthly trends to provide insights on procurement efficiency and supplier relationships.',
        'financial_position': 'This will analyze your general ledger data including transaction volumes, account balances, monthly trends, and financial health indicators to provide insights on financial performance and position.',
        'employee_attendance': 'This will analyze your employee attendance data including attendance rates, working hours, late arrivals, department performance, and monthly trends to provide insights on workforce productivity and attendance patterns.'
    };

    $('#category').change(function() {
        const selectedCategory = $(this).val();
        const description = categoryDescriptions[selectedCategory] || 'Select a category to see what data will be analyzed.';
        $('#category-description').html('<p class="mb-0">' + description + '</p>');
    });

    // Set initial description
    $('#category').trigger('change');
});
</script>
@endsection
