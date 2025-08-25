<?php

namespace App\Services;

use App\Models\AIEvaluation;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\GeneralLedger;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class AIEvaluationService
{
    /**
     * Generate AI evaluation for a specific category
     */
    public function generateEvaluation(string $category, Carbon $periodStart = null, Carbon $periodEnd = null): array
    {
        try {
            // Gather data based on category
            $data = $this->gatherDataForCategory($category, $periodStart, $periodEnd);
            
            // Log the gathered data for debugging
            \Log::info('AI Evaluation Data Gathered:', [
                'category' => $category,
                'data_summary' => array_key_exists('summary', $data) ? $data['summary'] : 'No summary',
                'data_keys' => array_keys($data)
            ]);
            
            // Generate prompt for the category
            $prompt = $this->generatePrompt($category, $data);
            
            // Call Gemini AI
            $aiResponse = $this->callGeminiAI($prompt);
            
            // Parse AI response
            $parsedResponse = $this->parseAIResponse($aiResponse);
            
            return [
                'success' => true,
                'data' => $data,
                'ai_response' => $aiResponse,
                'content' => $parsedResponse['full_response'],
                'insights' => $parsedResponse['insights'],
                'recommendations' => $parsedResponse['recommendations'],
            ];
            
        } catch (Exception $e) {
            Log::error('AI Evaluation failed: ' . $e->getMessage(), [
                'category' => $category,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Gather data for a specific evaluation category
     */
    private function gatherDataForCategory(string $category, Carbon $periodStart = null, Carbon $periodEnd = null): array
    {
        $companyId = auth()->user()->currentCompany->id;
        
        switch ($category) {
            case AIEvaluation::CATEGORY_SALES_ORDER:
                return $this->gatherSalesOrderData($companyId, $periodStart, $periodEnd);
                
            case AIEvaluation::CATEGORY_PURCHASE_ORDER:
                return $this->gatherPurchaseOrderData($companyId, $periodStart, $periodEnd);
                
            case AIEvaluation::CATEGORY_FINANCIAL_POSITION:
                return $this->gatherFinancialPositionData($companyId, $periodStart, $periodEnd);
                
            case AIEvaluation::CATEGORY_EMPLOYEE_ATTENDANCE:
                return $this->gatherEmployeeAttendanceData($companyId, $periodStart, $periodEnd);
                
            default:
                throw new Exception("Unknown evaluation category: {$category}");
        }
    }

    /**
     * Gather sales order data for analysis
     */
    private function gatherSalesOrderData(int $companyId, Carbon $periodStart = null, Carbon $periodEnd = null): array
    {
        try {
            $query = SalesOrder::where('company_id', $companyId)
                ->with(['customer', 'productLines.product', 'statusLogs']);
                
            if ($periodStart && $periodEnd) {
                $query->whereBetween('created_at', [$periodStart, $periodEnd]);
            }
            
            $salesOrders = $query->get();
            
            \Log::info('Sales Order Query:', [
                'company_id' => $companyId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_found' => $salesOrders->count()
            ]);
        } catch (Exception $e) {
            \Log::error('Error gathering sales order data: ' . $e->getMessage());
            return [
                'summary' => [
                    'total_orders' => 0,
                    'total_revenue' => 0,
                    'completed_orders' => 0,
                    'completion_rate' => 0,
                    'error' => $e->getMessage()
                ],
                'top_customers' => [],
                'monthly_trends' => [],
                'recent_orders' => [],
            ];
        }
        
        $totalOrders = $salesOrders->count();
        $totalRevenue = $salesOrders->sum('total');
        $completedOrders = $salesOrders->where('status', 'completed')->count();
        
        // Get all unique statuses for debugging
        $allStatuses = $salesOrders->pluck('status')->unique()->values();
        
        $topCustomers = $salesOrders->groupBy('customer_id')
            ->map(function ($orders) {
                return [
                    'customer_name' => $orders->first()->customer->name,
                    'total_orders' => $orders->count(),
                    'total_revenue' => $orders->sum('total'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(5);
            
        $monthlyTrends = $salesOrders->groupBy(function ($order) {
            return $order->created_at->format('Y-m');
        })->map(function ($orders) {
            return [
                'month' => $orders->first()->created_at->format('M Y'),
                'total_orders' => $orders->count(),
                'total_revenue' => $orders->sum('total'),
            ];
        });
        

        
        return [
            'summary' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'completed_orders' => $completedOrders,
                'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                'all_statuses' => $allStatuses->toArray(),
            ],
            'top_customers' => $topCustomers->values(),
            'monthly_trends' => $monthlyTrends->values(),
            'recent_orders' => $salesOrders->take(10)->map(function ($order) {
                return [
                    'number' => $order->number,
                    'customer' => $order->customer->name ?? 'Unknown',
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('M d, Y'),
                ];
            }),
        ];
    }

    /**
     * Gather purchase order data for analysis
     */
    private function gatherPurchaseOrderData(int $companyId, Carbon $periodStart = null, Carbon $periodEnd = null): array
    {
        $query = PurchaseOrder::where('company_id', $companyId)
            ->with(['supplier', 'productLines.product', 'statusLogs']);
            
        if ($periodStart && $periodEnd) {
            $query->whereBetween('created_at', [$periodStart, $periodEnd]);
        }
        
        $purchaseOrders = $query->get();
        
        $totalOrders = $purchaseOrders->count();
        $totalAmount = $purchaseOrders->sum('total');
        $completedOrders = $purchaseOrders->where('status', 'completed')->count();
        
        // Get all unique statuses for debugging
        $allStatuses = $purchaseOrders->pluck('status')->unique()->values();
        
        $topSuppliers = $purchaseOrders->groupBy('supplier_id')
            ->map(function ($orders) {
                return [
                    'supplier_name' => $orders->first()->supplier->name,
                    'total_orders' => $orders->count(),
                    'total_amount' => $orders->sum('total'),
                ];
            })
            ->sortByDesc('total_amount')
            ->take(5);
            
        $monthlyTrends = $purchaseOrders->groupBy(function ($order) {
            return $order->created_at->format('Y-m');
        })->map(function ($orders) {
            return [
                'month' => $orders->first()->created_at->format('M Y'),
                'total_orders' => $orders->count(),
                'total_amount' => $orders->sum('total'),
            ];
        });
        

        
        return [
            'summary' => [
                'total_orders' => $totalOrders,
                'total_amount' => $totalAmount,
                'completed_orders' => $completedOrders,
                'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                'all_statuses' => $allStatuses->toArray(),
            ],
            'top_suppliers' => $topSuppliers->values(),
            'monthly_trends' => $monthlyTrends->values(),
            'recent_orders' => $purchaseOrders->take(10)->map(function ($order) {
                return [
                    'number' => $order->number,
                    'supplier' => $order->supplier->name,
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('M d, Y'),
                ];
            }),
        ];
    }

    /**
     * Gather financial position data for analysis
     */
    private function gatherFinancialPositionData(int $companyId, Carbon $periodStart = null, Carbon $periodEnd = null): array
    {
        $query = GeneralLedger::where('company_id', $companyId)
            ->with(['details.account']);
            
        if ($periodStart && $periodEnd) {
            $query->whereBetween('date', [$periodStart, $periodEnd]);
        }
        
        $ledgers = $query->get();
        
        $totalTransactions = $ledgers->count();
        $totalDebits = $ledgers->sum(function ($ledger) {
            return $ledger->debits()->sum('value');
        });
        $totalCredits = $ledgers->sum(function ($ledger) {
            return $ledger->credits()->sum('value');
        });
        
        $accountBalances = $ledgers->flatMap(function ($ledger) {
            return $ledger->details;
        })->groupBy('account_id')->map(function ($details) {
            $account = $details->first()->account;
            $debits = $details->where('type', 'debit')->sum('value');
            $credits = $details->where('type', 'credit')->sum('value');
            
            return [
                'account_code' => $account->code,
                'account_name' => $account->name,
                'account_type' => $account->type,
                'debits' => $debits,
                'credits' => $credits,
                'balance' => $debits - $credits,
            ];
        });
        
        $monthlyTrends = $ledgers->groupBy(function ($ledger) {
            return $ledger->date->format('Y-m');
        })->map(function ($ledgers) {
            return [
                'month' => $ledgers->first()->date->format('M Y'),
                'total_transactions' => $ledgers->count(),
                'total_debits' => $ledgers->sum(function ($ledger) {
                    return $ledger->debits()->sum('value');
                }),
                'total_credits' => $ledgers->sum(function ($ledger) {
                    return $ledger->credits()->sum('value');
                }),
            ];
        });
        
        return [
            'summary' => [
                'total_transactions' => $totalTransactions,
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'net_position' => $totalDebits - $totalCredits,
            ],
            'account_balances' => $accountBalances->values(),
            'monthly_trends' => $monthlyTrends->values(),
            'recent_transactions' => $ledgers->take(10)->map(function ($ledger) {
                return [
                    'number' => $ledger->number,
                    'type' => $ledger->type,
                    'total' => $ledger->total,
                    'date' => $ledger->date->format('M d, Y'),
                    'description' => $ledger->description,
                ];
            }),
        ];
    }

    /**
     * Gather employee attendance data for analysis
     */
    private function gatherEmployeeAttendanceData(int $companyId, Carbon $periodStart = null, Carbon $periodEnd = null): array
    {
        $query = Attendance::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with(['employee.user', 'employee.department']);
        
        if ($periodStart && $periodEnd) {
            $query->whereBetween('date', [$periodStart, $periodEnd]);
        }
        
        $attendances = $query->get();
        
        $totalRecords = $attendances->count();
        $approvedRecords = $attendances->where('status', 'approved')->count();
        $pendingRecords = $attendances->where('status', 'pending')->count();
        $rejectedRecords = $attendances->where('status', 'rejected')->count();
        
        $employeeStats = $attendances->groupBy('employee_id')->map(function ($records) {
            $employee = $records->first()->employee;
            $totalDays = $records->count();
            $approvedDays = $records->where('status', 'approved')->count();
            $lateDays = $records->filter(function ($record) {
                return $record->isLate();
            })->count();
            
            $totalHours = $records->where('status', 'approved')->sum(function ($record) {
                return $record->working_hours;
            });
            
            return [
                'employee_name' => $employee->user->name,
                'department' => $employee->department->name,
                'position' => $employee->position,
                'total_days' => $totalDays,
                'approved_days' => $approvedDays,
                'late_days' => $lateDays,
                'total_hours' => $totalHours,
                'attendance_rate' => $totalDays > 0 ? round(($approvedDays / $totalDays) * 100, 2) : 0,
                'average_hours_per_day' => $approvedDays > 0 ? round($totalHours / $approvedDays, 2) : 0,
            ];
        });
        
        $departmentStats = $attendances->groupBy('employee.department_id')->map(function ($records) {
            $department = $records->first()->employee->department;
            $totalDays = $records->count();
            $approvedDays = $records->where('status', 'approved')->count();
            
            return [
                'department_name' => $department->name,
                'total_days' => $totalDays,
                'approved_days' => $approvedDays,
                'attendance_rate' => $totalDays > 0 ? round(($approvedDays / $totalDays) * 100, 2) : 0,
            ];
        });
        
        $monthlyTrends = $attendances->groupBy(function ($record) {
            return $record->date->format('Y-m');
        })->map(function ($records) {
            return [
                'month' => $records->first()->date->format('M Y'),
                'total_records' => $records->count(),
                'approved_records' => $records->where('status', 'approved')->count(),
                'pending_records' => $records->where('status', 'pending')->count(),
            ];
        });
        
        return [
            'summary' => [
                'total_records' => $totalRecords,
                'approved_records' => $approvedRecords,
                'pending_records' => $pendingRecords,
                'rejected_records' => $rejectedRecords,
                'approval_rate' => $totalRecords > 0 ? round(($approvedRecords / $totalRecords) * 100, 2) : 0,
            ],
            'employee_stats' => $employeeStats->values(),
            'department_stats' => $departmentStats->values(),
            'monthly_trends' => $monthlyTrends->values(),
        ];
    }

    /**
     * Generate prompt for the AI based on category and data
     */
    private function generatePrompt(string $category, array $data): string
    {
        $basePrompt = config("ai.evaluation.prompts.{$category}", '');
        
        $dataSummary = json_encode($data, JSON_PRETTY_PRINT);
        
        return "{$basePrompt}\n\nSilakan analisis data berikut dan berikan respons terstruktur dengan format berikut:\n\nWARASAN:\n- [Daftar wawasan utama dan observasi]\n- [Identifikasi tren dan pola]\n\nREKOMENDASI:\n- [Daftar rekomendasi spesifik untuk peningkatan]\n- [Sarankan langkah-langkah yang dapat dilakukan]\n\nANALISIS:\n[Berikan penilaian keseluruhan dan ringkasan]\n\nData yang akan dianalisis:\n{$dataSummary}";
    }

    /**
     * Call Gemini AI API
     */
    private function callGeminiAI(string $prompt): string
    {
        $config = config('ai.gemini');
        
        // Debug: Log the configuration and prompt
        \Log::info('Gemini API Config:', [
            'base_url' => $config['base_url'],
            'model' => $config['model'],
            'api_key' => $config['api_key'] ? '***' . substr($config['api_key'], -4) : 'NULL',
            'prompt_length' => strlen($prompt)
        ]);
        
        $url = "{$config['base_url']}/models/{$config['model']}:generateContent";
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ]
        ];
        
        \Log::info('Gemini API Request:', [
            'url' => $url,
            'payload_size' => strlen(json_encode($payload))
        ]);
        
        $response = Http::timeout($config['timeout'])
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $config['api_key'],
            ])
            ->post($url, $payload);
        
        \Log::info('Gemini API Response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        if ($response->failed()) {
            throw new Exception('Gemini AI API call failed: ' . $response->body());
        }
        
        $responseData = $response->json();
        
        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Invalid response format from Gemini AI: ' . json_encode($responseData));
        }
        
        return $responseData['candidates'][0]['content']['parts'][0]['text'];
    }

    /**
     * Parse AI response to extract insights and recommendations
     */
    private function parseAIResponse(string $response): array
    {
        // Clean the response - remove excessive asterisks and formatting
        $cleanResponse = preg_replace('/\*{3,}/', '', $response);
        $cleanResponse = preg_replace('/\*{2}/', '', $cleanResponse);
        $cleanResponse = trim($cleanResponse);
        
        // Split into sections and extract insights and recommendations
        $insights = [];
        $recommendations = [];
        
        // Look for structured responses (both English and Indonesian)
        if (preg_match('/(insights?|warasan).*?:(.*?)(?=(recommendations?|rekomendasi)|$)/is', $cleanResponse, $matches)) {
            $insightText = trim($matches[2]);
            $insightLines = array_filter(array_map('trim', explode("\n", $insightText)));
            foreach ($insightLines as $line) {
                if (!empty($line) && strlen($line) > 10) {
                    $insights[] = $line;
                }
            }
        }
        
        if (preg_match('/(recommendations?|rekomendasi).*?:(.*?)(?=(insights?|warasan)|$)/is', $cleanResponse, $matches)) {
            $recommendationText = trim($matches[2]);
            $recommendationLines = array_filter(array_map('trim', explode("\n", $recommendationText)));
            foreach ($recommendationLines as $line) {
                if (!empty($line) && strlen($line) > 10) {
                    $recommendations[] = $line;
                }
            }
        }
        
        // If no structured sections found, try to extract from numbered lists
        if (empty($insights) && empty($recommendations)) {
            $lines = explode("\n", $cleanResponse);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strlen($line) < 10) continue;
                
                // Look for numbered or bulleted items
                if (preg_match('/^\d+\.|^[-*•]\s/', $line)) {
                    if (stripos($line, 'insight') !== false || stripos($line, 'observation') !== false || 
                        stripos($line, 'trend') !== false || stripos($line, 'pattern') !== false ||
                        stripos($line, 'wawasan') !== false || stripos($line, 'observasi') !== false || 
                        stripos($line, 'tren') !== false || stripos($line, 'pola') !== false) {
                        $insights[] = $line;
                    } elseif (stripos($line, 'recommend') !== false || stripos($line, 'suggestion') !== false || 
                             stripos($line, 'improve') !== false || stripos($line, 'action') !== false ||
                             stripos($line, 'rekomendasi') !== false || stripos($line, 'saran') !== false || 
                             stripos($line, 'tingkatkan') !== false || stripos($line, 'tindakan') !== false) {
                        $recommendations[] = $line;
                    }
                }
            }
        }
        
        // If still no insights/recommendations, create them from the response
        if (empty($insights) && empty($recommendations)) {
            $paragraphs = array_filter(array_map('trim', explode("\n\n", $cleanResponse)));
            foreach ($paragraphs as $paragraph) {
                if (strlen($paragraph) > 50) {
                    if (stripos($paragraph, 'insight') !== false || stripos($paragraph, 'observation') !== false) {
                        $insights[] = $paragraph;
                    } elseif (stripos($paragraph, 'recommend') !== false || stripos($paragraph, 'suggestion') !== false) {
                        $recommendations[] = $paragraph;
                    }
                }
            }
        }
        
        return [
            'insights' => $insights ?: ['Analisis berhasil diselesaikan'],
            'recommendations' => $recommendations ?: ['Tinjau data untuk peluang peningkatan yang spesifik'],
            'full_response' => $cleanResponse,
        ];
    }
}
