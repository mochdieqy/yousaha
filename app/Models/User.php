<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'phone',
        'birthday',
        'gender',
        'marital_status',
        'identity_number',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'birthday' => 'date',
        'verify_at' => 'datetime',
    ];

    /**
     * Get the companies owned by the user.
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'owner');
    }

    /**
     * Get the departments managed by the user.
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    /**
     * Get the employee record for this user.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get the employees managed by this user.
     */
    public function managedEmployees()
    {
        return $this->hasMany(Employee::class, 'manager');
    }

    /**
     * Get the current company for the user.
     */
    public function getCurrentCompanyAttribute()
    {
        // First check session for current company
        if (session('current_company_id')) {
            $company = Company::find(session('current_company_id'));
            if ($company) {
                // Check if user owns this company or is an employee
                if ($company->owner === $this->id) {
                    return $company;
                }
                
                $employee = $this->employee;
                if ($employee && $employee->company_id === $company->id) {
                    return $company;
                }
            }
        }
        
        // Fallback: check if user owns a company
        $company = $this->companies()->first();
        if ($company) {
            return $company;
        }
        
        // If not owner, check if user is an employee
        $employee = $this->employee;
        if ($employee && $employee->company) {
            return $employee->company;
        }
        
        return null;
    }
}
