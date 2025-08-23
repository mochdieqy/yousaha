<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
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
        'customer_id',
        'salesperson',
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
     * Get the company that owns the sales order.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the warehouse for this sales order.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the customer for this sales order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product lines for this sales order.
     */
    public function productLines()
    {
        return $this->hasMany(SalesOrderProductLine::class);
    }

    /**
     * Get the status logs for this sales order.
     */
    public function statusLogs()
    {
        return $this->hasMany(SalesOrderStatusLog::class);
    }

    /**
     * Get the deliveries for this sales order.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Check if the sales order is overdue.
     */
    public function isOverdue()
    {
        return $this->deadline->isPast() && !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Get the total quantity of all products.
     */
    public function getTotalQuantityAttribute()
    {
        return $this->productLines()->sum('quantity');
    }
}
