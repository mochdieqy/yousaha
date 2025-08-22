<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'receive_from',
        'scheduled_at',
        'reference',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the company that owns the receipt.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the supplier for this receipt.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'receive_from');
    }

    /**
     * Get the product lines for this receipt.
     */
    public function productLines()
    {
        return $this->hasMany(ReceiptProductLine::class);
    }

    /**
     * Get the status logs for this receipt.
     */
    public function statusLogs()
    {
        return $this->hasMany(ReceiptStatusLog::class);
    }

    /**
     * Check if the receipt is overdue.
     */
    public function isOverdue()
    {
        return $this->scheduled_at->isPast() && !in_array($this->status, ['received', 'cancelled']);
    }

    /**
     * Get the total quantity of all products.
     */
    public function getTotalQuantityAttribute()
    {
        return $this->productLines()->sum('quantity');
    }
}
