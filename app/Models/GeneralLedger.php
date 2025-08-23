<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedger extends Model
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
        'type',
        'date',
        'note',
        'total',
        'reference',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
    ];

    /**
     * Get the company that owns the general ledger.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the general ledger details.
     */
    public function details()
    {
        return $this->hasMany(GeneralLedgerDetail::class);
    }

    /**
     * Get the debit entries.
     */
    public function debits()
    {
        return $this->hasMany(GeneralLedgerDetail::class)->where('type', 'debit');
    }

    /**
     * Get the credit entries.
     */
    public function credits()
    {
        return $this->hasMany(GeneralLedgerDetail::class)->where('type', 'credit');
    }

    /**
     * Check if the ledger is balanced.
     */
    public function isBalanced()
    {
        $debitTotal = $this->debits()->sum('value');
        $creditTotal = $this->credits()->sum('value');
        
        return abs($debitTotal - $creditTotal) < 0.01;
    }
}
