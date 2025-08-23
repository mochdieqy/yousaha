<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Company;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the demo company
        $company = Company::where('name', 'Yousaha Demo Company')->first();
        
        if (!$company) {
            $this->command->error('Demo company not found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Creating customers for company: ' . $company->name);

        // Create individual customers
        $this->createIndividualCustomers($company);
        
        // Create company customers
        $this->createCompanyCustomers($company);

        $this->command->info('Customer seeding completed successfully!');
    }

    private function createIndividualCustomers($company)
    {
        $individualCustomers = [
            [
                'name' => 'John Doe',
                'type' => 'individual',
                'phone' => '+62-812-3456-7001',
                'email' => 'john.doe@email.com',
                'address' => 'Jl. Individual Customer No. 1, Jakarta Selatan',
            ],
            [
                'name' => 'Jane Smith',
                'type' => 'individual',
                'phone' => '+62-812-3456-7002',
                'email' => 'jane.smith@email.com',
                'address' => 'Jl. Individual Customer No. 2, Bandung',
            ],
            [
                'name' => 'Michael Johnson',
                'type' => 'individual',
                'phone' => '+62-812-3456-7003',
                'email' => 'michael.johnson@email.com',
                'address' => 'Jl. Individual Customer No. 3, Surabaya',
            ],
            [
                'name' => 'Emily Davis',
                'type' => 'individual',
                'phone' => '+62-812-3456-7004',
                'email' => 'emily.davis@email.com',
                'address' => 'Jl. Individual Customer No. 4, Medan',
            ],
            [
                'name' => 'David Wilson',
                'type' => 'individual',
                'phone' => '+62-812-3456-7005',
                'email' => 'david.wilson@email.com',
                'address' => 'Jl. Individual Customer No. 5, Semarang',
            ],
            [
                'name' => 'Lisa Brown',
                'type' => 'individual',
                'phone' => '+62-812-3456-7006',
                'email' => 'lisa.brown@email.com',
                'address' => 'Jl. Individual Customer No. 6, Yogyakarta',
            ],
            [
                'name' => 'Robert Taylor',
                'type' => 'individual',
                'phone' => '+62-812-3456-7007',
                'email' => 'robert.taylor@email.com',
                'address' => 'Jl. Individual Customer No. 7, Palembang',
            ],
            [
                'name' => 'Amanda Miller',
                'type' => 'individual',
                'phone' => '+62-812-3456-7008',
                'email' => 'amanda.miller@email.com',
                'address' => 'Jl. Individual Customer No. 8, Makassar',
            ],
        ];

        foreach ($individualCustomers as $customerData) {
            Customer::firstOrCreate([
                'name' => $customerData['name'],
                'company_id' => $company->id,
            ], array_merge($customerData, [
                'company_id' => $company->id,
            ]));
        }

        $this->command->info('Created ' . count($individualCustomers) . ' individual customers');
    }

    private function createCompanyCustomers($company)
    {
        $companyCustomers = [
            [
                'name' => 'PT Maju Bersama',
                'type' => 'company',
                'phone' => '+62-21-1234-5678',
                'email' => 'info@majubersama.co.id',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            ],
            [
                'name' => 'CV Sukses Mandiri',
                'type' => 'company',
                'phone' => '+62-22-2345-6789',
                'email' => 'contact@suksesmandiri.co.id',
                'address' => 'Jl. Asia Afrika No. 45, Bandung',
            ],
            [
                'name' => 'PT Jaya Abadi',
                'type' => 'company',
                'phone' => '+62-31-3456-7890',
                'email' => 'sales@jayaabadi.co.id',
                'address' => 'Jl. Tunjungan No. 67, Surabaya',
            ],
            [
                'name' => 'UD Makmur Sejahtera',
                'type' => 'company',
                'phone' => '+62-61-4567-8901',
                'email' => 'info@makmursejahtera.co.id',
                'address' => 'Jl. Diponegoro No. 89, Medan',
            ],
            [
                'name' => 'PT Berkah Jaya',
                'type' => 'company',
                'phone' => '+62-24-5678-9012',
                'email' => 'contact@berkahjaya.co.id',
                'address' => 'Jl. Pandanaran No. 12, Semarang',
            ],
            [
                'name' => 'CV Sejahtera Abadi',
                'type' => 'company',
                'phone' => '+62-27-6789-0123',
                'email' => 'sales@sejahteraabadi.co.id',
                'address' => 'Jl. Malioboro No. 34, Yogyakarta',
            ],
            [
                'name' => 'PT Indah Permai',
                'type' => 'company',
                'phone' => '+62-71-7890-1234',
                'email' => 'info@indahpermai.co.id',
                'address' => 'Jl. Jenderal Sudirman No. 56, Palembang',
            ],
            [
                'name' => 'UD Makmur Bersama',
                'type' => 'company',
                'phone' => '+62-41-8901-2345',
                'email' => 'contact@makmurbersama.co.id',
                'address' => 'Jl. Pengayoman No. 78, Makassar',
            ],
        ];

        foreach ($companyCustomers as $customerData) {
            Customer::firstOrCreate([
                'name' => $customerData['name'],
                'company_id' => $company->id,
            ], array_merge($customerData, [
                'company_id' => $company->id,
            ]));
        }

        $this->command->info('Created ' . count($companyCustomers) . ' company customers');
    }
}
