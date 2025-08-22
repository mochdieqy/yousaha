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
        'delivery_address',
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
     * Get the company that owns the delivery.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
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
}
