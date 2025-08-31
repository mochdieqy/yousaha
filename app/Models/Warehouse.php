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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    /**
     * Check if warehouse has any stock.
     */
    public function hasStock()
    {
        return $this->stocks()->exists();
    }

    /**
     * Get warehouse display name with code.
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->name} ({$this->code})";
    }

    /**
     * Scope to filter by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to search warehouses.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }
}
