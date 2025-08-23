<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
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
        'delivery_address',
        'scheduled_at',
        'reference',
        'status',
        'sales_order_id',
        'customer_id',
        'number',
        'notes',
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
     * Get the company that owns the delivery.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the warehouse for this delivery.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the sales order for this delivery.
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /**
     * Get the customer for this delivery.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product lines for this delivery.
     */
    public function productLines()
    {
        return $this->hasMany(DeliveryProductLine::class);
    }

    /**
     * Get the status logs for this delivery.
     */
    public function statusLogs()
    {
        return $this->hasMany(DeliveryStatusLog::class);
    }

    /**
     * Check if the delivery is overdue.
     */
    public function isOverdue()
    {
        return $this->scheduled_at->isPast() && !in_array($this->status, ['delivered', 'cancelled']);
    }

    /**
     * Get the total quantity of all products.
     */
    public function getTotalQuantityAttribute()
    {
        return $this->productLines()->sum('quantity');
    }

    /**
     * Get the current status from the latest status log.
     */
    public function getCurrentStatusAttribute()
    {
        $latestStatusLog = $this->statusLogs()->latest('changed_at')->first();
        return $latestStatusLog ? $latestStatusLog->status : $this->status;
    }

    /**
     * Check if the delivery can be edited.
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'waiting']);
    }

    /**
     * Check if the delivery can be processed for goods issue.
     */
    public function canBeProcessed()
    {
        return $this->status === 'ready';
    }
}
