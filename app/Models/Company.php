<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner',
        'name',
        'address',
        'phone',
        'website',
    ];

    /**
     * Get the owner of the company.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    /**
     * Get the accounts for the company.
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get the products for the company.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the customers for the company.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the suppliers for the company.
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * Get the general ledgers for the company.
     */
    public function generalLedgers()
    {
        return $this->hasMany(GeneralLedger::class);
    }

    /**
     * Get the expenses for the company.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the incomes for the company.
     */
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    /**
     * Get the internal transfers for the company.
     */
    public function internalTransfers()
    {
        return $this->hasMany(InternalTransfer::class);
    }

    /**
     * Get the sales orders for the company.
     */
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    /**
     * Get the purchase orders for the company.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the warehouses for the company.
     */
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    /**
     * Get the stocks for the company.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the receipts for the company.
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Get the deliveries for the company.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get the departments for the company.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the employees for the company.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the assets for the company.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Get the users associated with this company (owners and employees).
     */
    public function users()
    {
        // Get company owner
        $owner = $this->owner;
        
        // Get employees
        $employees = $this->employees()->with('user')->get()->pluck('user');
        
        // Combine and return unique users
        return collect([$owner])->merge($employees)->filter()->unique('id');
    }

    /**
     * Get a query builder for users associated with this company.
     */
    public function usersQuery()
    {
        // Get company owner ID
        $ownerId = $this->owner;
        
        // Get employee user IDs
        $employeeUserIds = $this->employees()->pluck('user_id')->toArray();
        
        // Combine owner and employee user IDs
        $userIds = array_merge([$ownerId], $employeeUserIds);
        $userIds = array_filter($userIds); // Remove null/empty values
        
        // Return query builder for these users
        return User::whereIn('id', $userIds);
    }
}
