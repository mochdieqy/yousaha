<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $query = Account::where('company_id', $company->id);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $accounts = $query->orderBy('code')->paginate(15);

        return view('pages.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $accountTypes = [
            'Asset' => 'Asset',
            'Liability' => 'Liability',
            'Equity' => 'Equity',
            'Revenue' => 'Revenue',
            'Expense' => 'Expense',
        ];

        return view('pages.accounts.create', compact('accountTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:accounts,code,NULL,id,company_id,' . $company->id,
            'name' => 'required|string|max:100',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Account::create([
                'company_id' => $company->id,
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
            ]);

            return redirect()->route('accounts.index')
                ->with('success', 'Account created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create account. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $account->company_id !== $company->id) {
            abort(403);
        }

        // Eager load relationships to avoid N+1 queries
        $account->load([
            'generalLedgerDetails.generalLedger',
            'expenseDetails.expense',
            'incomeDetails.income',
            'internalTransfersIn',
            'internalTransfersOut',
            'assets'
        ]);

        return view('pages.accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $account->company_id !== $company->id) {
            abort(403);
        }

        $accountTypes = [
            'Asset' => 'Asset',
            'Liability' => 'Liability',
            'Equity' => 'Equity',
            'Revenue' => 'Revenue',
            'Expense' => 'Expense',
        ];

        return view('pages.accounts.edit', compact('account', 'accountTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $account->company_id !== $company->id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:accounts,code,' . $account->id . ',id,company_id,' . $company->id,
            'name' => 'required|string|max:100',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $account->update([
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
            ]);

            return redirect()->route('accounts.index')
                ->with('success', 'Account updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update account. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $account->company_id !== $company->id) {
            abort(403);
        }

        // Check if this is a critical account that cannot be deleted
        if ($account->isCriticalAccount()) {
            return redirect()->back()
                ->with('error', 'This account cannot be deleted as it is a critical system account used in sales and purchase orders.');
        }

        // Use the model's canBeDeleted method for better encapsulation
        if (!$account->canBeDeleted()) {
            $reason = $account->getDeletionBlockReason();
            return redirect()->back()->with('error', $reason);
        }

        try {
            $account->delete();

            return redirect()->route('accounts.index')
                ->with('success', 'Account deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete account. Please try again.');
        }
    }
}
