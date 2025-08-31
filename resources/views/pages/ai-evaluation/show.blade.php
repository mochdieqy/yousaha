@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-robot text-primary me-2"></i>
                    {{ $evaluation->title }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ai-evaluation.index') }}">AI Evaluations</a></li>
                        <li class="breadcrumb-item active">View Evaluation</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex">
                @if($evaluation->isDraft())
                <a href="{{ route('ai-evaluation.edit', $evaluation) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </a>
                @endif
                
                @if($evaluation->isCompleted())
                <button type="button" 
                        class="btn btn-info me-2" 
                        onclick="confirmRegenerate({{ $evaluation->id }}, '{{ addslashes($evaluation->title) }}')">
                    <i class="fas fa-sync-alt me-2"></i>
                    Regenerate
                </button>
                @endif
                
                <a href="{{ route('ai-evaluation.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Evaluations
                </a>
            </div>
        </div>

        <!-- Evaluation Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Evaluation Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Category</label>
                                    <div>
                                        <span class="badge bg-info fs-6">{{ $evaluation->category_display_name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Status</label>
                                    <div>
                                        @if($evaluation->isCompleted())
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check me-1"></i>Completed
                                            </span>
                                        @elseif($evaluation->isDraft())
                                            <span class="badge bg-warning fs-6">
                                                <i class="fas fa-edit me-1"></i>Draft
                                            </span>
                                        @else
                                            <span class="badge bg-danger fs-6">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Failed
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Period</label>
                                    <div class="fs-6">
                                        @if($evaluation->period_start && $evaluation->period_end)
                                            {{ $evaluation->period_start->format('M d, Y') }} - {{ $evaluation->period_end->format('M d, Y') }}
                                        @elseif($evaluation->period_start)
                                            {{ $evaluation->period_start->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">All Time</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Generated By</label>
                                    <div class="fs-6">
                                        <i class="fas fa-user text-muted me-1"></i>
                                        {{ $evaluation->generatedByUser->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Created On</label>
                                    <div class="fs-6">
                                        <i class="fas fa-calendar text-muted me-1"></i>
                                        {{ $evaluation->created_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Last Updated</label>
                                    <div class="fs-6">
                                        <i class="fas fa-clock text-muted me-1"></i>
                                        {{ $evaluation->updated_at->format('M d, Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="d-flex flex-column">
                            <button type="button" 
                                    class="btn btn-danger mb-2" 
                                    onclick="confirmDelete({{ $evaluation->id }}, '{{ addslashes($evaluation->title) }}')">
                                <i class="fas fa-trash me-2"></i>
                                Delete Evaluation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($evaluation->isCompleted())
        <!-- AI-Generated Content -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-brain me-2"></i>
                    AI-Generated Analysis
                </h5>
            </div>
            <div class="card-body">
                <div class="ai-content">
                    @if(is_string($evaluation->content))
                        {!! nl2br(e($evaluation->content)) !!}
                    @elseif(is_array($evaluation->content))
                        <pre class="text-muted">{{ json_encode($evaluation->content, JSON_PRETTY_PRINT) }}</pre>
                    @else
                        <p class="text-muted">No content available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Key Insights -->
        @if($evaluation->insights && is_array($evaluation->insights) && count($evaluation->insights) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Key Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($evaluation->insights as $index => $insight)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-chart-line text-primary fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Insight {{ $index + 1 }}</h6>
                                <p class="mb-0 text-muted">{{ is_string($insight) ? $insight : json_encode($insight) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Recommendations -->
        @if($evaluation->recommendations && is_array($evaluation->recommendations) && count($evaluation->recommendations) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>
                    Recommendations
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($evaluation->recommendations as $index => $recommendation)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-arrow-up text-success fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Recommendation {{ $index + 1 }}</h6>
                                <p class="mb-0 text-muted">{{ is_string($recommendation) ? $recommendation : json_encode($recommendation) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Data Summary -->
        @if($evaluation->data_summary && is_array($evaluation->data_summary) && count($evaluation->data_summary) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-database me-2"></i>
                    Data Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($evaluation->data_summary as $key => $value)
                    <div class="col-md-4 mb-3">
                        <div class="text-center p-3 border rounded">
                            @if(is_numeric($value))
                                <h4 class="text-primary mb-1">{{ number_format($value) }}</h4>
                            @elseif(is_string($value))
                                <h4 class="text-primary mb-1">{{ $value }}</h4>
                            @elseif(is_array($value))
                                <h4 class="text-primary mb-1">{{ count($value) }} items</h4>
                            @else
                                <h4 class="text-primary mb-1">{{ json_encode($value) }}</h4>
                            @endif
                            <small class="text-muted text-uppercase">{{ str_replace('_', ' ', $key) }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @elseif($evaluation->isDraft())
        <!-- Draft Status Message -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-edit fa-3x text-warning mb-3"></i>
                <h5 class="text-warning">Evaluation is in Draft Status</h5>
                <p class="text-muted">This evaluation has been created but the AI analysis has not been generated yet.</p>
                <a href="{{ route('ai-evaluation.edit', $evaluation) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Complete Evaluation
                </a>
            </div>
        </div>
        @else
        <!-- Failed Status Message -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5 class="text-danger">Evaluation Generation Failed</h5>
                <p class="text-muted">The AI analysis could not be completed. Please try regenerating the evaluation.</p>
                <button type="button" 
                        class="btn btn-info" 
                        onclick="confirmRegenerate({{ $evaluation->id }}, '{{ addslashes($evaluation->title) }}')">
                    <i class="fas fa-sync-alt me-2"></i>
                    Try Again
                </button>
            </div>
        </div>
        @endif
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
                <p>Are you sure you want to delete the AI evaluation "<strong id="evaluationName"></strong>"?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Delete Evaluation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Regenerate Confirmation Modal -->
<div class="modal fade" id="regenerateModal" tabindex="-1" aria-labelledby="regenerateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateModalLabel">Confirm Regeneration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to regenerate the AI evaluation "<strong id="regenerateEvaluationName"></strong>"?</p>
                <p class="text-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    This will create a new analysis based on current data.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="regenerateForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-sync-alt me-2"></i>
                        Regenerate Evaluation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function confirmDelete(evaluationId, evaluationName) {
    document.getElementById('evaluationName').textContent = evaluationName;
    document.getElementById('deleteForm').action = `/ai-evaluation/${evaluationId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function confirmRegenerate(evaluationId, evaluationName) {
    document.getElementById('regenerateEvaluationName').textContent = evaluationName;
    document.getElementById('regenerateForm').action = `/ai-evaluation/${evaluationId}/regenerate`;
    
    const regenerateModal = new bootstrap.Modal(document.getElementById('regenerateModal'));
    regenerateModal.show();
}

// Handle form submissions with loading state
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.getElementById('deleteForm');
    const regenerateForm = document.getElementById('regenerateForm');
    
    if (deleteForm) {
        deleteForm.addEventListener('submit', function() {
            const submitBtn = deleteForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
            
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        });
    }
    
    if (regenerateForm) {
        regenerateForm.addEventListener('submit', function() {
            const submitBtn = regenerateForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Regenerating...';
            
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        });
    }
});
</script>
@endsection
