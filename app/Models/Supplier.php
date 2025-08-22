<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'type',
        'name',
        'address',
        'phone',
        'email',
    ];

    /**
     * The possible supplier types.
     */
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COMPANY = 'company';

    /**
     * Get the company that owns the supplier.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the purchase orders for this supplier.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the receipts from this supplier.
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'receive_from');
    }

    /**
     * Check if the supplier is an individual.
     */
    public function isIndividual()
    {
        return $this->type === self::TYPE_INDIVIDUAL;
    }

    /**
     * Check if the supplier is a company.
     */
    public function isCompany()
    {
        return $this->type === self::TYPE_COMPANY;
    }
}
