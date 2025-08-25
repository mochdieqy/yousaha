<?php

namespace App\Http\Controllers;

use App\Models\AIEvaluation;
use App\Services\AIEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AIEvaluationController extends Controller
{
    protected $aiEvaluationService;

    public function __construct(AIEvaluationService $aiEvaluationService)
    {
        $this->aiEvaluationService = $aiEvaluationService;
    }

    /**
     * Display a listing of AI evaluations.
     */
    public function index()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $evaluations = AIEvaluation::where('company_id', $company->id)
            ->with(['generatedByUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = config('ai.evaluation.categories');

        return view('pages.ai-evaluation.index', compact('evaluations', 'categories'));
    }

    /**
     * Show the form for creating a new AI evaluation.
     */
    public function create()
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $categories = config('ai.evaluation.categories');

        return view('pages.ai-evaluation.create', compact('categories'));
    }

    /**
     * Store a newly created AI evaluation in storage.
     */
    public function store(Request $request)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company) {
            return redirect()->route('company.choice')->with('error', 'Please select a company first.');
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|in:' . implode(',', array_keys(config('ai.evaluation.categories'))),
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'title' => 'required|string|max:255',
        ], [
            'category.required' => 'Please select an evaluation category.',
            'category.in' => 'The selected category is invalid.',
            'period_start.date' => 'Period start must be a valid date.',
            'period_end.date' => 'Period end must be a valid date.',
            'period_end.after_or_equal' => 'Period end must be after or equal to period start.',
            'title.required' => 'Evaluation title is required.',
            'title.max' => 'Evaluation title may not be greater than 255 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Create evaluation record with draft status
            $evaluation = AIEvaluation::create([
                'company_id' => $company->id,
                'category' => $request->category,
                'title' => $request->title,
                'period_start' => $request->period_start ? Carbon::parse($request->period_start) : null,
                'period_end' => $request->period_end ? Carbon::parse($request->period_end) : null,
                'status' => AIEvaluation::STATUS_DRAFT,
                'generated_by' => Auth::id(),
            ]);

            // Generate AI evaluation
            \Log::info('Starting AI evaluation generation', [
                'category' => $request->category,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'user_id' => Auth::id(),
                'company_id' => $company->id
            ]);
            
            $result = $this->aiEvaluationService->generateEvaluation(
                $request->category,
                $request->period_start ? Carbon::parse($request->period_start) : null,
                $request->period_end ? Carbon::parse($request->period_end) : null
            );
            
            \Log::info('AI evaluation result received', [
                'success' => $result['success'] ?? false,
                'has_content' => isset($result['content']),
                'has_insights' => isset($result['insights']),
                'has_recommendations' => isset($result['recommendations']),
                'result_keys' => array_keys($result)
            ]);

            if ($result['success']) {
                // Validate required fields exist
                if (!isset($result['content']) || !isset($result['insights']) || !isset($result['recommendations'])) {
                    \Log::error('AI Evaluation response missing required fields:', $result);
                    throw new \Exception('AI response is incomplete. Please try again.');
                }
                
                // Update evaluation with AI results
                $evaluation->update([
                    'content' => $result['content'],
                    'data_summary' => $result['data'],
                    'insights' => $result['insights'],
                    'recommendations' => $result['recommendations'],
                    'status' => AIEvaluation::STATUS_COMPLETED,
                ]);

                return redirect()->route('ai-evaluation.show', $evaluation)
                    ->with('success', 'AI evaluation generated successfully!');
            } else {
                // Update evaluation with error status
                $evaluation->update([
                    'status' => AIEvaluation::STATUS_FAILED,
                ]);

                return redirect()->back()
                    ->with('error', 'Failed to generate AI evaluation: ' . $result['error'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while generating the evaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified AI evaluation.
     */
    public function show(AIEvaluation $evaluation)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $evaluation->company_id !== $company->id) {
            return redirect()->route('ai-evaluation.index')
                ->with('error', 'Evaluation not found.');
        }

        $evaluation->load(['generatedByUser']);

        return view('pages.ai-evaluation.show', compact('evaluation'));
    }

    /**
     * Show the form for editing the specified AI evaluation.
     */
    public function edit(AIEvaluation $evaluation)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $evaluation->company_id !== $company->id) {
            return redirect()->route('ai-evaluation.index')
                ->with('error', 'Evaluation not found.');
        }

        if (!$evaluation->isDraft()) {
            return redirect()->route('ai-evaluation.show', $evaluation)
                ->with('error', 'Only draft evaluations can be edited.');
        }

        $categories = config('ai.evaluation.categories');

        return view('pages.ai-evaluation.edit', compact('evaluation', 'categories'));
    }

    /**
     * Update the specified AI evaluation in storage.
     */
    public function update(Request $request, AIEvaluation $evaluation)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $evaluation->company_id !== $company->id) {
            return redirect()->route('ai-evaluation.index')
                ->with('error', 'Evaluation not found.');
        }

        if (!$evaluation->isDraft()) {
            return redirect()->route('ai-evaluation.show', $evaluation)
                ->with('error', 'Only draft evaluations can be edited.');
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|in:' . implode(',', array_keys(config('ai.evaluation.categories'))),
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'title' => 'required|string|max:255',
        ], [
            'category.required' => 'Please select an evaluation category.',
            'category.in' => 'The selected category is invalid.',
            'period_start.date' => 'Period start must be a valid date.',
            'period_end.date' => 'Period end must be a valid date.',
            'period_end.after_or_equal' => 'Period end must be after or equal to period start.',
            'title.required' => 'Evaluation title is required.',
            'title.max' => 'Evaluation title may not be greater than 255 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Update evaluation record
            $evaluation->update([
                'category' => $request->category,
                'title' => $request->title,
                'period_start' => $request->period_start ? Carbon::parse($request->period_start) : null,
                'period_end' => $request->period_end ? Carbon::parse($request->period_end) : null,
            ]);

            // Regenerate AI evaluation
            $result = $this->aiEvaluationService->generateEvaluation(
                $request->category,
                $request->period_start ? Carbon::parse($request->period_start) : null,
                $request->period_end ? Carbon::parse($request->period_end) : null
            );

            if ($result['success']) {
                // Update evaluation with AI results
                $evaluation->update([
                    'content' => $result['ai_response'],
                    'data_summary' => $result['data'],
                    'insights' => $result['parsed_response']['insights'],
                    'recommendations' => $result['parsed_response']['recommendations'],
                    'status' => AIEvaluation::STATUS_COMPLETED,
                ]);

                return redirect()->route('ai-evaluation.show', $evaluation)
                    ->with('success', 'AI evaluation updated successfully!');
            } else {
                // Update evaluation with error status
                $evaluation->update([
                    'status' => AIEvaluation::STATUS_FAILED,
                ]);

                return redirect()->back()
                    ->with('error', 'Failed to update AI evaluation: ' . $result['error'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while updating the evaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified AI evaluation from storage.
     */
    public function destroy(AIEvaluation $evaluation)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $evaluation->company_id !== $company->id) {
            return redirect()->route('ai-evaluation.index')
                ->with('error', 'Evaluation not found.');
        }

        $evaluation->delete();

        return redirect()->route('ai-evaluation.index')
            ->with('success', 'Evaluation deleted successfully.');
    }

    /**
     * Regenerate AI evaluation for an existing evaluation.
     */
    public function regenerate(AIEvaluation $evaluation)
    {
        $company = Auth::user()->currentCompany;
        
        if (!$company || $evaluation->company_id !== $company->id) {
            return redirect()->route('ai-evaluation.index')
                ->with('error', 'Evaluation not found.');
        }

        try {
            // Regenerate AI evaluation
            $result = $this->aiEvaluationService->generateEvaluation(
                $evaluation->category,
                $evaluation->period_start,
                $evaluation->period_end
            );

            if ($result['success']) {
                // Update evaluation with AI results
                $evaluation->update([
                    'content' => $result['ai_response'],
                    'data_summary' => $result['data'],
                    'insights' => $result['parsed_response']['insights'],
                    'recommendations' => $result['parsed_response']['recommendations'],
                    'status' => AIEvaluation::STATUS_COMPLETED,
                ]);

                return redirect()->route('ai-evaluation.show', $evaluation)
                    ->with('success', 'AI evaluation regenerated successfully!');
            } else {
                // Update evaluation with error status
                $evaluation->update([
                    'status' => AIEvaluation::STATUS_FAILED,
                ]);

                return redirect()->back()
                    ->with('error', 'Failed to regenerate AI evaluation: ' . $result['error']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while regenerating the evaluation: ' . $e->getMessage());
        }
    }
}
