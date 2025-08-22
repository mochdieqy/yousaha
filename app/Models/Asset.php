<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'number',
        'name',
        'purchased_date',
        'account_asset',
        'quantity',
        'location',
        'reference',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchased_date' => 'date',
    ];

    /**
     * Get the company that owns the asset.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the asset account.
     */
    public function assetAccount()
    {
        return $this->belongsTo(Account::class, 'account_asset');
    }

    /**
     * Get the age of the asset in years.
     */
    public function getAgeInYearsAttribute()
    {
        return $this->purchased_date->diffInYears(now());
    }

    /**
     * Get the age of the asset in months.
     */
    public function getAgeInMonthsAttribute()
    {
        return $this->purchased_date->diffInMonths(now());
    }

    /**
     * Check if the asset is recently purchased (within 1 year).
     */
    public function isRecentlyPurchased()
    {
        return $this->purchased_date->isAfter(now()->subYear());
    }
}
