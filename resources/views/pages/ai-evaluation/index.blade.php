@extends('layouts.home')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-robot text-primary me-2"></i>
                    AI Evaluation Management
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">AI Evaluations</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('ai-evaluation.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Generate New Evaluation
            </a>
        </div>

        <!-- AI Evaluations Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            AI Evaluation List
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            <span class="badge bg-info text-white">
                                <i class="fas fa-building me-1"></i>
                                {{ Auth::user()->currentCompany->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Search and Filter Form -->
                <div class="p-3 border-bottom">
                    <form method="GET" action="{{ route('ai-evaluation.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search evaluations..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $key => $name)
                                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                        @if(request('search') || request('category') || request('status'))
                            <div class="col-12">
                                <a href="{{ route('ai-evaluation.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                @if($evaluations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Evaluation</th>
                                <th class="border-0">Category</th>
                                <th class="border-0">Period</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Generated By</th>
                                <th class="border-0">Created</th>
                                <th class="border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations as $evaluation)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-robot text-primary fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">
                                                <a href="{{ route('ai-evaluation.show', $evaluation) }}" class="text-decoration-none">
                                                    {{ $evaluation->title }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">ID: {{ $evaluation->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $evaluation->category_display_name }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($evaluation->period_start && $evaluation->period_end)
                                            <span class="fw-bold">{{ $evaluation->period_start->format('M d, Y') }} - {{ $evaluation->period_end->format('M d, Y') }}</span>
                                        @elseif($evaluation->period_start)
                                            <span class="fw-bold">{{ $evaluation->period_start->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-muted">All Time</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($evaluation->isCompleted())
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Completed
                                        </span>
                                    @elseif($evaluation->isDraft())
                                        <span class="badge bg-warning">
                                            <i class="fas fa-edit me-1"></i>Draft
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Failed
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                        <span>{{ $evaluation->generatedByUser->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $evaluation->created_at->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ $evaluation->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('ai-evaluation.show', $evaluation) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="View Evaluation">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($evaluation->isDraft())
                                        <a href="{{ route('ai-evaluation.edit', $evaluation) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit Evaluation">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                        
                                        @if($evaluation->isCompleted())
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-info" 
                                                title="Regenerate Evaluation"
                                                onclick="confirmRegenerate({{ $evaluation->id }}, '{{ addslashes($evaluation->title) }}')">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        @endif
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete Evaluation"
                                                onclick="confirmDelete({{ $evaluation->id }}, '{{ addslashes($evaluation->title) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No AI Evaluations Found</h5>
                    <p class="text-muted">Start by generating your first AI-powered business evaluation.</p>
                    <a href="{{ route('ai-evaluation.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Generate First Evaluation
                    </a>
                </div>
                @endif
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeDeleteModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the AI evaluation "<strong id="evaluationName"></strong>"?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeDeleteModal()">Cancel</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeRegenerateModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to regenerate the AI evaluation "<strong id="regenerateEvaluationName"></strong>"?</p>
                <p class="text-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    This will create a new analysis based on current data.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeRegenerateModal()">Cancel</button>
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
let deleteModalInstance = null;
let regenerateModalInstance = null;

function confirmDelete(evaluationId, evaluationName) {
    document.getElementById('evaluationName').textContent = evaluationName;
    document.getElementById('deleteForm').action = `/ai-evaluation/${evaluationId}`;
    
    deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModalInstance.show();
}

function confirmRegenerate(evaluationId, evaluationName) {
    document.getElementById('regenerateEvaluationName').textContent = evaluationName;
    document.getElementById('regenerateForm').action = `/ai-evaluation/${evaluationId}/regenerate`;
    
    regenerateModalInstance = new bootstrap.Modal(document.getElementById('regenerateModal'));
    regenerateModalInstance.show();
}

function closeDeleteModal() {
    // Method 1: Use stored instance
    if (deleteModalInstance) {
        deleteModalInstance.hide();
        return;
    }
    
    // Method 2: Try to get existing instance
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 3: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        newModal.hide();
    } catch (error) {
        console.error('Error closing delete modal:', error);
    }
    
    // Method 4: Manual hide using CSS classes
    const modalElement = document.getElementById('deleteModal');
    if (modalElement) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

function closeRegenerateModal() {
    // Method 1: Use stored instance
    if (regenerateModalInstance) {
        regenerateModalInstance.hide();
        return;
    }
    
    // Method 2: Try to get existing instance
    const modal = bootstrap.Modal.getInstance(document.getElementById('regenerateModal'));
    if (modal) {
        modal.hide();
        return;
    }
    
    // Method 3: Create new instance and hide immediately
    try {
        const newModal = new bootstrap.Modal(document.getElementById('regenerateModal'));
        newModal.hide();
    } catch (error) {
        console.error('Error closing regenerate modal:', error);
    }
    
    // Method 4: Manual hide using CSS classes
    const modalElement = document.getElementById('regenerateModal');
    if (modalElement) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const deleteModalElement = document.getElementById('deleteModal');
    const regenerateModalElement = document.getElementById('regenerateModal');
    const deleteForm = document.getElementById('deleteForm');
    const regenerateForm = document.getElementById('regenerateForm');
    
    // Close delete modal when clicking outside
    deleteModalElement.addEventListener('click', function(event) {
        if (event.target === deleteModalElement) {
            closeDeleteModal();
        }
    });
    
    // Close regenerate modal when clicking outside
    regenerateModalElement.addEventListener('click', function(event) {
        if (event.target === regenerateModalElement) {
            closeRegenerateModal();
        }
    });
    
    // Close modal when pressing ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
            closeRegenerateModal();
        }
    });
    
    // Handle delete form submission with loading state
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
    
    // Handle regenerate form submission with loading state
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
    
    // Auto-hide success/error messages after 5 seconds
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
