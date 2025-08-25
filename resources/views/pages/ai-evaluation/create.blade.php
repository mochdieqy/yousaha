@extends('layouts.home')

@section('title', 'Generate AI Evaluation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Generate AI Evaluation</h4>
                <div class="page-title-right">
                    <a href="{{ route('ai-evaluation.index') }}" class="btn btn-secondary mb-3">
                        <i class="fas fa-arrow-left"></i> Back to Evaluations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('ai-evaluation.store') }}" method="POST" id="ai-evaluation-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Evaluation Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
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
                                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
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
                                           value="{{ old('period_start') }}">
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
                                           value="{{ old('period_end') }}">
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
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('ai-evaluation.index') }}" class="btn btn-secondary me-2">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="generate-btn">
                                        <i class="fas fa-robot"></i> Generate AI Evaluation
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

@section('style')
<style>
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 0.375rem;
}

.loading-content {
    text-align: center;
    padding: 2rem;
}

.loading-spinner {
    font-size: 2rem;
    color: #0d6efd;
    margin-bottom: 1rem;
}

.progress-indicator {
    width: 100%;
    max-width: 300px;
    margin: 1rem auto;
}

.progress-step {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    opacity: 0.6;
}

.progress-step.active {
    opacity: 1;
    color: #0d6efd;
}

.progress-step.completed {
    opacity: 1;
    color: #198754;
}

.progress-step i {
    margin-right: 0.5rem;
    width: 16px;
}
</style>
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

    // Set initial description if category is pre-selected
    if ($('#category').val()) {
        $('#category').trigger('change');
    }

    // Handle form submission with loading state
    $('#ai-evaluation-form').on('submit', function() {
        const submitBtn = $('#generate-btn');
        const originalText = submitBtn.html();
        
        // Disable button and show loading state
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Generating AI Evaluation...');
        
        // Add loading overlay to the form
        const form = $(this);
        form.css('position', 'relative');
        
        const loadingOverlay = $(`
            <div class="loading-overlay">
                <div class="loading-content">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <h5>AI is analyzing your data...</h5>
                    <p class="text-muted">This may take a few minutes</p>
                    
                    <div class="progress-indicator">
                        <div class="progress-step active">
                            <i class="fas fa-database"></i>
                            <span>Gathering data...</span>
                        </div>
                        <div class="progress-step">
                            <i class="fas fa-brain"></i>
                            <span>AI processing...</span>
                        </div>
                        <div class="progress-step">
                            <i class="fas fa-chart-line"></i>
                            <span>Generating insights...</span>
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        form.append(loadingOverlay);
        
        // Simulate progress updates
        setTimeout(() => {
            $('.progress-step').eq(0).removeClass('active').addClass('completed');
            $('.progress-step').eq(1).addClass('active');
        }, 2000);
        
        setTimeout(() => {
            $('.progress-step').eq(1).removeClass('active').addClass('completed');
            $('.progress-step').eq(2).addClass('active');
        }, 4000);
        
        // Re-enable button after 5 seconds as fallback (in case of errors)
        setTimeout(function() {
            if (submitBtn.prop('disabled')) {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
                $('.loading-overlay').remove();
            }
        }, 300000); // 5 minutes timeout
    });
});
</script>
@endsection
