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
        'name',
        'number',
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
        'quantity' => 'integer',
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
    public function accountAsset()
    {
        return $this->belongsTo(Account::class, 'account_asset');
    }


}
