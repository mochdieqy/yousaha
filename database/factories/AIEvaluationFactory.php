<?php

namespace Database\Factories;

use App\Models\AIEvaluation;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AIEvaluation>
 */
class AIEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            AIEvaluation::CATEGORY_SALES_ORDER,
            AIEvaluation::CATEGORY_PURCHASE_ORDER,
            AIEvaluation::CATEGORY_FINANCIAL_POSITION,
            AIEvaluation::CATEGORY_EMPLOYEE_ATTENDANCE,
        ];

        $statuses = [
            AIEvaluation::STATUS_DRAFT,
            AIEvaluation::STATUS_COMPLETED,
            AIEvaluation::STATUS_FAILED,
        ];

        return [
            'company_id' => Company::factory(),
            'category' => $this->faker->randomElement($categories),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs(3, true),
            'data_summary' => [
                'summary' => [
                    'total_items' => $this->faker->numberBetween(100, 1000),
                    'total_value' => $this->faker->numberBetween(10000, 100000),
                    'completion_rate' => $this->faker->numberBetween(70, 95),
                ],
                'monthly_trends' => [
                    [
                        'month' => 'Jan 2024',
                        'total_items' => $this->faker->numberBetween(50, 200),
                        'total_value' => $this->faker->numberBetween(5000, 25000),
                    ],
                    [
                        'month' => 'Feb 2024',
                        'total_items' => $this->faker->numberBetween(50, 200),
                        'total_value' => $this->faker->numberBetween(5000, 25000),
                    ],
                ],
            ],
            'insights' => [
                'Sales performance shows consistent growth over the analyzed period.',
                'Customer retention rate is above industry average.',
                'Peak sales periods align with seasonal business patterns.',
            ],
            'recommendations' => [
                'Focus on expanding customer base during peak periods.',
                'Consider implementing customer loyalty programs.',
                'Optimize inventory management for seasonal demand.',
            ],
            'evaluation_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'period_start' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
            'period_end' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'status' => $this->faker->randomElement($statuses),
            'generated_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the evaluation is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AIEvaluation::STATUS_COMPLETED,
        ]);
    }

    /**
     * Indicate that the evaluation is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AIEvaluation::STATUS_DRAFT,
        ]);
    }

    /**
     * Indicate that the evaluation failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AIEvaluation::STATUS_FAILED,
        ]);
    }

    /**
     * Indicate that the evaluation is for sales orders.
     */
    public function salesOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => AIEvaluation::CATEGORY_SALES_ORDER,
            'title' => 'Sales Performance Analysis - ' . $this->faker->words(3, true),
        ]);
    }

    /**
     * Indicate that the evaluation is for purchase orders.
     */
    public function purchaseOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => AIEvaluation::CATEGORY_PURCHASE_ORDER,
            'title' => 'Procurement Efficiency Analysis - ' . $this->faker->words(3, true),
        ]);
    }

    /**
     * Indicate that the evaluation is for financial position.
     */
    public function financialPosition(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => AIEvaluation::CATEGORY_FINANCIAL_POSITION,
            'title' => 'Financial Health Assessment - ' . $this->faker->words(3, true),
        ]);
    }

    /**
     * Indicate that the evaluation is for employee attendance.
     */
    public function employeeAttendance(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => AIEvaluation::CATEGORY_EMPLOYEE_ATTENDANCE,
            'title' => 'Workforce Productivity Analysis - ' . $this->faker->words(3, true),
        ]);
    }
}
