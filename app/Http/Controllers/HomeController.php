<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);
        
        $company = new Company();
        $company->owner = Auth::id();
        $company->name = $request->name;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->website = $request->website;
        $company->save();
        
        // Assign Company Owner role to the user
        $user = Auth::user();
        $companyOwnerRole = Role::where('name', 'Company Owner')->first();
        if ($companyOwnerRole) {
            $user->assignRole($companyOwnerRole);
        }
        
        return redirect()->route('home')->with('success', 'Company created successfully!');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);
        
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
