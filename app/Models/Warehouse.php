<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'address',
    ];

    /**
     * Get the company that owns the warehouse.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the stocks in this warehouse.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the total number of products in this warehouse.
     */
    public function getTotalProductsAttribute()
    {
        return $this->stocks()->count();
    }

    /**
     * Get the total quantity of all products in this warehouse.
     */
    public function getTotalQuantityAttribute()
    {
        return $this->stocks()->sum('quantity_total');
    }
}
