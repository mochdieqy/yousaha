<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Roles are now created automatically by the test base class
    }

    /** @test */
    public function it_creates_basic_finance_accounts_when_company_is_created()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Act as the user
        $this->actingAs($user);
        
        // Company data
        $companyData = [
            'name' => 'Test Company',
            'address' => '123 Test Street',
            'phone' => '1234567890',
            'website' => 'https://testcompany.com'
        ];
        
        // Check initial account count
        $initialAccountCount = Account::count();
        
        // Create company through the controller
        $response = $this->post(route('company.store'), $companyData);
        
        // Assert company was created
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'owner' => $user->id
        ]);
        
        // Get the created company
        $company = Company::where('name', 'Test Company')->first();
        
        // Check final account count
        $finalAccountCount = Account::count();
        $accountsCreated = $finalAccountCount - $initialAccountCount;
        
        // Assert that basic finance accounts were created
        $this->assertDatabaseHas('accounts', [
            'company_id' => $company->id,
            'code' => '1000',
            'name' => 'Cash',
            'type' => 'Asset'
        ]);
        
        $this->assertDatabaseHas('accounts', [
            'company_id' => $company->id,
            'code' => '2000',
            'name' => 'Accounts Payable',
            'type' => 'Liability'
        ]);
        
        $this->assertDatabaseHas('accounts', [
            'company_id' => $company->id,
            'code' => '3000',
            'name' => 'Owner\'s Equity',
            'type' => 'Equity'
        ]);
        
        $this->assertDatabaseHas('accounts', [
            'company_id' => $company->id,
            'code' => '4000',
            'name' => 'Sales Revenue',
            'type' => 'Revenue'
        ]);
        
        $this->assertDatabaseHas('accounts', [
            'company_id' => $company->id,
            'code' => '5000',
            'name' => 'Cost of Goods Sold',
            'type' => 'Expense'
        ]);
        
        // Assert that exactly 21 new accounts were created for this company
        $this->assertEquals(21, $company->accounts()->count(), "Company should have exactly 21 accounts");
        
        // Assert user was assigned Company Owner role
        $this->assertTrue($user->hasRole('Company Owner'));
        
        // Assert redirect to home
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Company created successfully!');
    }

    /** @test */
    public function it_validates_company_creation_data()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Act as the user
        $this->actingAs($user);
        
        // Test with invalid data
        $invalidData = [
            'name' => '', // Required field missing
            'address' => '', // Required field missing
            'phone' => 'invalid-phone', // Invalid format
            'website' => 'not-a-url' // Invalid URL
        ];
        
        $response = $this->post(route('company.store'), $invalidData);
        
        // Assert validation errors - check for the actual errors that occur
        $response->assertSessionHasErrors(['name', 'address', 'website']);
        
        // Assert no company was created
        $this->assertDatabaseMissing('companies', [
            'owner' => $user->id
        ]);
        
        // Assert no accounts were created
        $this->assertEquals(0, Account::count());
    }

    /** @test */
    public function it_creates_company_with_valid_data_and_optional_website()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Act as the user
        $this->actingAs($user);
        
        // Company data without website
        $companyData = [
            'name' => 'Test Company No Website',
            'address' => '456 Test Avenue',
            'phone' => '0987654321'
            // website is optional
        ];
        
        // Create company through the controller
        $response = $this->post(route('company.store'), $companyData);
        
        // Assert company was created
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company No Website',
            'owner' => $user->id,
            'website' => null
        ]);
        
        // Get the created company
        $company = Company::where('name', 'Test Company No Website')->first();
        
        // Assert that basic finance accounts were created
        $this->assertEquals(21, $company->accounts()->count(), "Company should have exactly 21 accounts");
        
        // Assert redirect to home
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Company created successfully!');
    }
}
