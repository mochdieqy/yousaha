<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerDetail;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    /**
     * Display the financial reports index page
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        return view('pages.financial-reports.index', compact('company'));
    }

    /**
     * Generate Statement of Financial Position (Balance Sheet)
     */
    public function statementOfFinancialPosition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company = Auth::user()->currentCompany;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get accounts grouped by type
        $assets = Account::where('company_id', $company->id)
            ->where('type', 'asset')
            ->orderBy('code')
            ->get();

        $liabilities = Account::where('company_id', $company->id)
            ->where('type', 'liability')
            ->orderBy('code')
            ->get();

        $equity = Account::where('company_id', $company->id)
            ->where('type', 'equity')
            ->orderBy('code')
            ->get();

        // Calculate balances for the period
        $this->calculateAccountBalances($assets, $startDate, $endDate);
        $this->calculateAccountBalances($liabilities, $startDate, $endDate);
        $this->calculateAccountBalances($equity, $startDate, $endDate);

        // Calculate totals
        $totalAssets = $assets->sum('period_balance');
        $totalLiabilities = $liabilities->sum('period_balance');
        $totalEquity = $equity->sum('period_balance');

        $data = [
            'company' => $company,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
        ];

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('pdf.financial-reports.statement-of-financial-position', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('statement-of-financial-position-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
        }

        return view('pages.financial-reports.statement-of-financial-position', $data);
    }

    /**
     * Generate Profit and Loss Statement
     */
    public function profitAndLoss(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company = Auth::user()->currentCompany;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get revenue and expense accounts
        $revenueAccounts = Account::where('company_id', $company->id)
            ->where('type', 'revenue')
            ->orderBy('code')
            ->get();

        $expenseAccounts = Account::where('company_id', $company->id)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();

        // Calculate balances for the period
        $this->calculateAccountBalances($revenueAccounts, $startDate, $endDate);
        $this->calculateAccountBalances($expenseAccounts, $startDate, $endDate);

        // Calculate totals
        $totalRevenue = $revenueAccounts->sum('period_balance');
        $totalExpenses = $expenseAccounts->sum('period_balance');
        $netIncome = $totalRevenue - $totalExpenses;

        $data = [
            'company' => $company,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'revenueAccounts' => $revenueAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netIncome' => $netIncome,
        ];

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('pdf.financial-reports.profit-and-loss', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('profit-and-loss-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
        }

        return view('pages.financial-reports.profit-and-loss', $data);
    }

    /**
     * Generate General Ledger History
     */
    public function generalLedgerHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company = Auth::user()->currentCompany;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get all accounts for selection
        $accounts = Account::where('company_id', $company->id)
            ->orderBy('code')
            ->get();

        // Build query for general ledger entries
        $query = GeneralLedger::where('company_id', $company->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['details.account'])
            ->orderBy('date')
            ->orderBy('number');

        // Filter by specific account if provided
        if ($request->filled('account_id')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('account_id', $request->account_id);
            });
        }

        $generalLedgers = $query->get();

        // Group entries by date for better organization
        $groupedEntries = $generalLedgers->groupBy(function ($ledger) {
            return $ledger->date->format('Y-m-d');
        });

        $data = [
            'company' => $company,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'accounts' => $accounts,
            'selectedAccountId' => $request->account_id,
            'groupedEntries' => $groupedEntries,
            'generalLedgers' => $generalLedgers,
        ];

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('pdf.financial-reports.general-ledger-history', $data);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('general-ledger-history-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
        }

        return view('pages.financial-reports.general-ledger-history', $data);
    }

    /**
     * Calculate account balances for a specific period
     */
    private function calculateAccountBalances($accounts, $startDate, $endDate)
    {
        foreach ($accounts as $account) {
            // Get opening balance (balance before start date)
            $openingBalance = GeneralLedgerDetail::whereHas('generalLedger', function ($query) use ($startDate) {
                $query->where('company_id', auth()->user()->currentCompany->id)
                    ->where('date', '<', $startDate);
            })
            ->where('account_id', $account->id)
            ->get()
            ->sum(function ($detail) use ($account) {
                $impact = $this->calculateEntryImpact($detail, $account->type);
                \Log::info("Opening balance calculation for {$account->code} ({$account->type}): {$detail->type} {$detail->value} = {$impact}");
                return $impact;
            });

            // Get period transactions
            $periodTransactions = GeneralLedgerDetail::whereHas('generalLedger', function ($query) use ($startDate, $endDate) {
                $query->where('company_id', auth()->user()->currentCompany->id)
                    ->whereBetween('date', [$startDate, $endDate]);
            })
            ->where('account_id', $account->id)
            ->get()
            ->sum(function ($detail) use ($account) {
                $impact = $this->calculateEntryImpact($detail, $account->type);
                \Log::info("Period transaction calculation for {$account->code} ({$account->type}): {$detail->type} {$detail->value} = {$impact}");
                return $impact;
            });

            // Calculate period balance
            $account->opening_balance = $openingBalance;
            $account->period_balance = $openingBalance + $periodTransactions;
            
            \Log::info("Account {$account->code} ({$account->type}): Opening={$openingBalance}, Period={$periodTransactions}, Final={$account->period_balance}");
        }
    }

    /**
     * Calculate the impact of a general ledger entry on an account balance
     * Based on account type and entry type (debit/credit)
     */
    private function calculateEntryImpact($detail, $accountType)
    {
        $value = $detail->value;
        $isDebit = $detail->type === 'debit';
        
        switch (strtolower($accountType)) {
            case 'asset':
                // Assets increase with debits, decrease with credits
                return $isDebit ? $value : -$value;
                
            case 'liability':
                // Liabilities increase with credits, decrease with debits
                return $isDebit ? -$value : $value;
                
            case 'equity':
                // Equity increases with credits, decreases with debits
                return $isDebit ? -$value : $value;
                
            case 'revenue':
                // Revenue increases with credits, decreases with debits
                return $isDebit ? -$value : $value;
                
            case 'expense':
                // Expenses increase with debits, decrease with credits
                return $isDebit ? $value : -$value;
                
            default:
                // Default behavior (old logic) - log for debugging
                \Log::warning("Unknown account type: {$accountType}, using default calculation");
                return $isDebit ? $value : -$value;
        }
    }
}
