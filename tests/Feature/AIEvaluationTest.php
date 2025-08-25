<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\AIEvaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AIEvaluationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Get existing role
        $role = Role::where('name', 'Company Owner')->first();
        if (!$role) {
            $role = Role::create(['name' => 'Company Owner']);
        }
        
        // Create user
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
        
        // Create company
        $this->company = Company::factory()->create([
            'owner' => $this->user->id
        ]);
        
        // Set current company for user
        $this->user->currentCompany = $this->company;
    }

    /** @test */
    public function user_can_view_ai_evaluation_index()
    {
        $response = $this->actingAs($this->user)
            ->get('/ai-evaluation');

        $response->assertStatus(200);
        $response->assertViewIs('pages.ai-evaluation.index');
    }

    /** @test */
    public function user_can_view_create_ai_evaluation_form()
    {
        $response = $this->actingAs($this->user)
            ->get('/ai-evaluation/create');

        $response->assertStatus(200);
        $response->assertViewIs('pages.ai-evaluation.create');
    }

    /** @test */
    public function user_can_create_ai_evaluation()
    {
        $evaluationData = [
            'title' => 'Test Sales Analysis',
            'category' => 'sales_order',
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ];

        $response = $this->actingAs($this->user)
            ->post('/ai-evaluation', $evaluationData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('ai_evaluations', [
            'title' => 'Test Sales Analysis',
            'category' => 'sales_order',
            'company_id' => $this->company->id,
        ]);
    }

    /** @test */
    public function user_can_view_ai_evaluation()
    {
        $evaluation = AIEvaluation::factory()->create([
            'company_id' => $this->company->id,
            'generated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/ai-evaluation/{$evaluation->id}");

        $response->assertStatus(200);
        $response->assertViewIs('pages.ai-evaluation.show');
    }

    /** @test */
    public function user_can_edit_ai_evaluation()
    {
        $evaluation = AIEvaluation::factory()->draft()->create([
            'company_id' => $this->company->id,
            'generated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/ai-evaluation/{$evaluation->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('pages.ai-evaluation.edit');
    }

    /** @test */
    public function user_can_update_ai_evaluation()
    {
        $evaluation = AIEvaluation::factory()->draft()->create([
            'company_id' => $this->company->id,
            'generated_by' => $this->user->id,
        ]);

        $updateData = [
            'title' => 'Updated Sales Analysis',
            'category' => 'sales_order',
            'period_start' => '2024-06-01',
            'period_end' => '2024-12-31',
        ];

        $response = $this->actingAs($this->user)
            ->put("/ai-evaluation/{$evaluation->id}", $updateData);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('ai_evaluations', [
            'id' => $evaluation->id,
            'title' => 'Updated Sales Analysis',
        ]);
    }

    /** @test */
    public function user_can_delete_ai_evaluation()
    {
        $evaluation = AIEvaluation::factory()->create([
            'company_id' => $this->company->id,
            'generated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/ai-evaluation/{$evaluation->id}");

        $response->assertRedirect();
        
        $this->assertDatabaseMissing('ai_evaluations', [
            'id' => $evaluation->id,
        ]);
    }

    /** @test */
    public function user_cannot_access_other_company_evaluation()
    {
        $otherCompany = Company::factory()->create();
        $evaluation = AIEvaluation::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/ai-evaluation/{$evaluation->id}");

        $response->assertRedirect('/ai-evaluation');
    }

    /** @test */
    public function validation_works_for_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post('/ai-evaluation', []);

        $response->assertSessionHasErrors(['title', 'category']);
    }

    /** @test */
    public function validation_works_for_date_range()
    {
        $evaluationData = [
            'title' => 'Test Analysis',
            'category' => 'sales_order',
            'period_start' => '2024-12-31',
            'period_end' => '2024-01-01', // End before start
        ];

        $response = $this->actingAs($this->user)
            ->post('/ai-evaluation', $evaluationData);

        $response->assertSessionHasErrors(['period_end']);
    }
}
