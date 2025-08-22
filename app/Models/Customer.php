<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
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
     * The possible customer types.
     */
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COMPANY = 'company';

    /**
     * Get the company that owns the customer.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the sales orders for this customer.
     */
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    /**
     * Check if the customer is an individual.
     */
    public function isIndividual()
    {
        return $this->type === self::TYPE_INDIVIDUAL;
    }

    /**
     * Check if the customer is a company.
     */
    public function isCompany()
    {
        return $this->type === self::TYPE_COMPANY;
    }
}
