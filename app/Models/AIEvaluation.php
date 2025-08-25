<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIEvaluation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ai_evaluations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'category',
        'title',
        'content',
        'data_summary',
        'insights',
        'recommendations',
        'evaluation_date',
        'period_start',
        'period_end',
        'status',
        'generated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'evaluation_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'data_summary' => 'array',
        'insights' => 'array',
        'recommendations' => 'array',
    ];

    /**
     * The possible evaluation categories.
     */
    const CATEGORY_SALES_ORDER = 'sales_order';
    const CATEGORY_PURCHASE_ORDER = 'purchase_order';
    const CATEGORY_FINANCIAL_POSITION = 'financial_position';
    const CATEGORY_EMPLOYEE_ATTENDANCE = 'employee_attendance';

    /**
     * The possible status values.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Get the company that owns the evaluation.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who generated the evaluation.
     */
    public function generatedByUser()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get the category display name.
     */
    public function getCategoryDisplayNameAttribute()
    {
        return config("ai.evaluation.categories.{$this->category}", $this->category);
    }

    /**
     * Check if the evaluation is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the evaluation is draft.
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if the evaluation failed.
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get the evaluation period in a readable format.
     */
    public function getPeriodDisplayAttribute()
    {
        if ($this->period_start && $this->period_end) {
            return $this->period_start->format('M d, Y') . ' - ' . $this->period_end->format('M d, Y');
        }
        
        if ($this->period_start) {
            return $this->period_start->format('M d, Y');
        }
        
        return 'N/A';
    }

    /**
     * Scope query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope query to filter by company.
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
