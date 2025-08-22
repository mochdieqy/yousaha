<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
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
        'manager_id',
        'description',
        'location',
        'parent_id',
    ];

    /**
     * Get the company that owns the department.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the manager of the department.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the parent department.
     */
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Get the child departments.
     */
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * Get the employees in this department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Check if this department has a parent.
     */
    public function hasParent()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if this department has children.
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Get all descendants (recursive children).
     */
    public function descendants()
    {
        return $this->hasMany(Department::class, 'parent_id')->with('descendants');
    }
}
