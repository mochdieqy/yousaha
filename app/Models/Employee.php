<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'department_id',
        'number',
        'position',
        'level',
        'join_date',
        'manager',
        'work_location',
        'work_arrangement',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'join_date' => 'date',
    ];

    /**
     * The possible work arrangements.
     */
    const WORK_ARRANGEMENT_WFO = 'WFO';
    const WORK_ARRANGEMENT_WFH = 'WFH';
    const WORK_ARRANGEMENT_WFA = 'WFA';

    /**
     * Get the company that owns the employee.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user associated with this employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department for this employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the manager for this employee.
     */
    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager');
    }

    /**
     * Get the payroll information for this employee.
     */
    public function payroll()
    {
        return $this->hasOne(Payroll::class);
    }

    /**
     * Get the attendances for this employee.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the time offs for this employee.
     */
    public function timeOffs()
    {
        return $this->hasMany(TimeOff::class);
    }

    /**
     * Check if the employee works from office.
     */
    public function isWFO()
    {
        return $this->work_arrangement === self::WORK_ARRANGEMENT_WFO;
    }

    /**
     * Check if the employee works from home.
     */
    public function isWFH()
    {
        return $this->work_arrangement === self::WORK_ARRANGEMENT_WFH;
    }

    /**
     * Check if the employee works from anywhere.
     */
    public function isWFA()
    {
        return $this->work_arrangement === self::WORK_ARRANGEMENT_WFA;
    }

    /**
     * Get the years of service.
     */
    public function getYearsOfServiceAttribute()
    {
        return $this->join_date->diffInYears(now());
    }
}
