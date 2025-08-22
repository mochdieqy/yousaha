<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'payment_account_bank',
        'payment_account_number',
        'tax_number',
        'employment_insurance_number',
        'health_insurance_number',
    ];

    /**
     * Get the employee that owns this payroll.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Check if tax number is provided.
     */
    public function hasTaxNumber()
    {
        return !is_null($this->tax_number);
    }

    /**
     * Check if employment insurance number is provided.
     */
    public function hasEmploymentInsurance()
    {
        return !is_null($this->employment_insurance_number);
    }

    /**
     * Check if health insurance number is provided.
     */
    public function hasHealthInsurance()
    {
        return !is_null($this->health_insurance_number);
    }
}
