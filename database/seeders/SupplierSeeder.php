<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Company;

class SupplierSeeder extends Seeder
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

        $this->command->info('Creating suppliers for company: ' . $company->name);

        // Create individual suppliers
        $this->createIndividualSuppliers($company);
        
        // Create company suppliers
        $this->createCompanySuppliers($company);

        $this->command->info('Supplier seeding completed successfully!');
    }

    private function createIndividualSuppliers($company)
    {
        $individualSuppliers = [
            [
                'name' => 'Ahmad Rizki',
                'type' => 'individual',
                'phone' => '+62-812-3456-7890',
                'email' => 'ahmad.rizki@email.com',
                'address' => 'Jl. Individual Supplier No. 1, Jakarta Selatan',
            ],
            [
                'name' => 'Sarah Wijaya',
                'type' => 'individual',
                'phone' => '+62-812-3456-7891',
                'email' => 'sarah.wijaya@email.com',
                'address' => 'Jl. Individual Supplier No. 2, Bandung',
            ],
            [
                'name' => 'Budi Santoso',
                'type' => 'individual',
                'phone' => '+62-812-3456-7892',
                'email' => 'budi.santoso@email.com',
                'address' => 'Jl. Individual Supplier No. 3, Surabaya',
            ],
            [
                'name' => 'Dewi Putri',
                'type' => 'individual',
                'phone' => '+62-812-3456-7893',
                'email' => 'dewi.putri@email.com',
                'address' => 'Jl. Individual Supplier No. 4, Medan',
            ],
            [
                'name' => 'Rudi Hermawan',
                'type' => 'individual',
                'phone' => '+62-812-3456-7894',
                'email' => 'rudi.hermawan@email.com',
                'address' => 'Jl. Individual Supplier No. 5, Semarang',
            ],
            [
                'name' => 'Nina Sari',
                'type' => 'individual',
                'phone' => '+62-812-3456-7895',
                'email' => 'nina.sari@email.com',
                'address' => 'Jl. Individual Supplier No. 6, Yogyakarta',
            ],
            [
                'name' => 'Agus Setiawan',
                'type' => 'individual',
                'phone' => '+62-812-3456-7896',
                'email' => 'agus.setiawan@email.com',
                'address' => 'Jl. Individual Supplier No. 7, Palembang',
            ],
            [
                'name' => 'Maya Indah',
                'type' => 'individual',
                'phone' => '+62-812-3456-7897',
                'email' => 'maya.indah@email.com',
                'address' => 'Jl. Individual Supplier No. 8, Makassar',
            ],
        ];

        foreach ($individualSuppliers as $supplierData) {
            Supplier::firstOrCreate([
                'name' => $supplierData['name'],
                'company_id' => $company->id,
            ], array_merge($supplierData, [
                'company_id' => $company->id,
            ]));
        }

        $this->command->info('Created ' . count($individualSuppliers) . ' individual suppliers');
    }

    private function createCompanySuppliers($company)
    {
        $companySuppliers = [
            [
                'name' => 'PT Maju Bersama Teknologi',
                'type' => 'company',
                'phone' => '+62-21-1234-5678',
                'email' => 'info@majubersama.com',
                'address' => 'Jl. Gatot Subroto No. 123, Jakarta Selatan, DKI Jakarta 12950',
            ],
            [
                'name' => 'CV Sukses Mandiri',
                'type' => 'company',
                'phone' => '+62-22-2345-6789',
                'email' => 'contact@suksesmandiri.co.id',
                'address' => 'Jl. Asia Afrika No. 456, Bandung, Jawa Barat 40111',
            ],
            [
                'name' => 'PT Global Solutions Indonesia',
                'type' => 'company',
                'phone' => '+62-31-3456-7890',
                'email' => 'hello@globalsolutions.id',
                'address' => 'Jl. Basuki Rahmat No. 789, Surabaya, Jawa Timur 60271',
            ],
            [
                'name' => 'UD Makmur Jaya',
                'type' => 'company',
                'phone' => '+62-61-4567-8901',
                'email' => 'info@makmurjaya.com',
                'address' => 'Jl. Diponegoro No. 321, Medan, Sumatera Utara 20112',
            ],
            [
                'name' => 'PT Digital Innovation Hub',
                'type' => 'company',
                'phone' => '+62-24-5678-9012',
                'email' => 'contact@digitalhub.id',
                'address' => 'Jl. Pandanaran No. 654, Semarang, Jawa Tengah 50134',
            ],
            [
                'name' => 'CV Kreasi Digital',
                'type' => 'company',
                'phone' => '+62-274-6789-0123',
                'email' => 'hello@kreasidigital.com',
                'address' => 'Jl. Malioboro No. 987, Yogyakarta, DI Yogyakarta 55213',
            ],
            [
                'name' => 'PT Nusantara Sejahtera',
                'type' => 'company',
                'phone' => '+62-711-7890-1234',
                'email' => 'info@nusantarasejahtera.co.id',
                'address' => 'Jl. Jenderal Sudirman No. 147, Palembang, Sumatera Selatan 30129',
            ],
            [
                'name' => 'UD Teknologi Makassar',
                'type' => 'company',
                'phone' => '+62-411-8901-2345',
                'email' => 'contact@teknologimakassar.com',
                'address' => 'Jl. Pengayoman No. 258, Makassar, Sulawesi Selatan 90111',
            ],
            [
                'name' => 'PT Smart Solutions',
                'type' => 'company',
                'phone' => '+62-21-9012-3456',
                'email' => 'hello@smartsolutions.id',
                'address' => 'Jl. Sudirman No. 369, Jakarta Pusat, DKI Jakarta 12190',
            ],
            [
                'name' => 'CV Inovasi Digital',
                'type' => 'company',
                'phone' => '+62-22-0123-4567',
                'email' => 'info@inovasidigital.co.id',
                'address' => 'Jl. Dago No. 741, Bandung, Jawa Barat 40135',
            ],
            [
                'name' => 'PT Future Technology',
                'type' => 'company',
                'phone' => '+62-31-1234-5678',
                'email' => 'contact@futuretech.id',
                'address' => 'Jl. Tunjungan No. 852, Surabaya, Jawa Timur 60275',
            ],
            [
                'name' => 'UD Modern Solutions',
                'type' => 'company',
                'phone' => '+62-61-2345-6789',
                'email' => 'hello@modernsolutions.com',
                'address' => 'Jl. Merdeka No. 963, Medan, Sumatera Utara 20212',
            ],
        ];

        foreach ($companySuppliers as $supplierData) {
            Supplier::firstOrCreate([
                'name' => $supplierData['name'],
                'company_id' => $company->id,
            ], array_merge($supplierData, [
                'company_id' => $company->id,
            ]));
        }

        $this->command->info('Created ' . count($companySuppliers) . ' company suppliers');
    }
}
