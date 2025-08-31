<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'warehouse_id',
        'number',
        'supplier_id',
        'requestor',
        'activities',
        'total',
        'status',
        'deadline',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => 'decimal:2',
        'deadline' => 'date',
    ];

    /**
     * Get the company that owns the purchase order.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the warehouse for this purchase order.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the supplier for this purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the product lines for this purchase order.
     */
    public function productLines()
    {
        return $this->hasMany(PurchaseOrderProductLine::class);
    }

    /**
     * Get the status logs for this purchase order.
     */
    public function statusLogs()
    {
        return $this->hasMany(PurchaseOrderStatusLog::class);
    }

    /**
     * Get the receipts related to this purchase order.
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'reference', 'number');
    }

    /**
     * Check if the purchase order is overdue.
     */
    public function isOverdue()
    {
        return $this->deadline->isPast() && !in_array($this->status, ['done', 'cancel']);
    }

    /**
     * Get the total quantity of all products.
     */
    public function getTotalQuantityAttribute()
    {
        return $this->productLines()->sum('quantity');
    }
}
