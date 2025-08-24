<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Company;
use App\Models\Account;
use App\Models\Employee;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    public function Home() {
        $user = Auth::user();
        
        // Check if user owns a company
        $company = Company::where('owner', $user->id)->first();
        
        if ($company) {
            // User owns a company, assign Company Owner role if not already assigned
            if (!$user->hasRole('Company Owner')) {
                $companyOwnerRole = Role::where('name', 'Company Owner')->first();
                if ($companyOwnerRole) {
                    $user->assignRole($companyOwnerRole);
                }
            }
            
            // Show home page
            return view('pages.home.index', compact('company'));
        }
        
        // Check if user is an employee in a company
        $employee = Employee::where('user_id', $user->id)->first();
        
        if ($employee) {
            // User is an employee, assign Employee role if not already assigned
            if (!$user->hasRole('Employee')) {
                $employeeRole = Role::where('name', 'Employee')->first();
                if ($employeeRole) {
                    $user->assignRole($employeeRole);
                }
            }
            
            // Show home page
            $company = $employee->company;
            return view('pages.home.index', compact('company'));
        }
        
        // User is not in any company, redirect to company choice page
        return redirect()->route('company.choice');
    }
    
    public function companyChoice() {
        return view('pages.company.choice');
    }
    
    public function createCompany() {
        return view('pages.company.create');
    }
    
    public function storeCompany(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $company = new Company();
        $company->owner = Auth::id();
        $company->name = $request->name;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->website = $request->website;
        $company->save();
        
        // Create basic finance accounts for the company
        $this->createBasicFinanceAccounts($company);
        
        // Assign Company Owner role to the user
        $user = Auth::user();
        $companyOwnerRole = Role::where('name', 'Company Owner')->first();
        if ($companyOwnerRole) {
            $user->assignRole($companyOwnerRole);
        }
        
        return redirect()->route('home')->with('success', 'Company created successfully!');
    }
    
    /**
     * Create basic finance accounts for a newly created company
     * Creates 21 standard accounts covering Assets, Liabilities, Equity, Revenue, and Expenses
     */
    private function createBasicFinanceAccounts(Company $company)
    {
        $basicAccounts = [
            // Asset Accounts
            ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset', 'balance' => 0.00],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'balance' => 0.00],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'Asset', 'balance' => 0.00],
            ['code' => '1300', 'name' => 'Prepaid Expenses', 'type' => 'Asset', 'balance' => 0.00],
            ['code' => '1400', 'name' => 'Fixed Assets', 'type' => 'Asset', 'balance' => 0.00],
            ['code' => '1500', 'name' => 'Accumulated Depreciation', 'type' => 'Asset', 'balance' => 0.00],
            
            // Liability Accounts
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'balance' => 0.00],
            ['code' => '2100', 'name' => 'Accrued Expenses', 'type' => 'Liability', 'balance' => 0.00],
            ['code' => '2200', 'name' => 'Short-term Loans', 'type' => 'Liability', 'balance' => 0.00],
            ['code' => '2300', 'name' => 'Long-term Loans', 'type' => 'Liability', 'balance' => 0.00],
            
            // Equity Accounts
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'Equity', 'balance' => 0.00],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'Equity', 'balance' => 0.00],
            ['code' => '3200', 'name' => 'Current Year Earnings', 'type' => 'Equity', 'balance' => 0.00],
            
            // Revenue Accounts
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'Revenue', 'balance' => 0.00],
            ['code' => '4100', 'name' => 'Other Income', 'type' => 'Revenue', 'balance' => 0.00],
            
            // Expense Accounts
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5100', 'name' => 'Operating Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5200', 'name' => 'Payroll Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5300', 'name' => 'Marketing Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5400', 'name' => 'Administrative Expenses', 'type' => 'Expense', 'balance' => 0.00],
            ['code' => '5500', 'name' => 'Depreciation Expense', 'type' => 'Expense', 'balance' => 0.00],
        ];
        
        foreach ($basicAccounts as $accountData) {
            Account::create([
                'company_id' => $company->id,
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'type' => $accountData['type'],
                'balance' => $accountData['balance'],
            ]);
        }
    }
    
    public function editCompany() {
        $user = Auth::user();
        
        // Check if user owns a company
        $company = Company::where('owner', $user->id)->first();
        
        if (!$company) {
            return redirect()->route('home')->with('error', 'You can only edit companies you own.');
        }
        
        return view('pages.company.edit', compact('company'));
    }
    
    public function updateCompany(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $company = Company::where('owner', $user->id)->first();
        
        if (!$company) {
            return redirect()->route('home')->with('error', 'You can only update companies you own.');
        }
        
        $company->name = $request->name;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->website = $request->website;
        $company->save();
        
        return redirect()->route('home')->with('success', 'Company updated successfully!');
    }
    
    public function employeeInvitation() {
        return view('pages.company.employee-invitation');
    }
}
