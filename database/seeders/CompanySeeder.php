<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'SafeTech Industries',
                'description' => 'Manufacturing safety solutions',
                'contact_email' => 'info@safetech.com',
                'contact_phone' => '+1-555-0101',
                'is_active' => true,
            ],
            [
                'name' => 'Global Construction Co.',
                'description' => 'Building safety excellence',
                'contact_email' => 'safety@globalconstruction.com',
                'contact_phone' => '+1-555-0102',
                'is_active' => true,
            ],
            [
                'name' => 'Energy Solutions Ltd',
                'description' => 'Power sector safety management',
                'contact_email' => 'hse@energysolutions.com',
                'contact_phone' => '+1-555-0103',
                'is_active' => true,
            ],
            [
                'name' => 'Transport Safety Group',
                'description' => 'Logistics and transport compliance',
                'contact_email' => 'compliance@transportsafety.com',
                'contact_phone' => '+1-555-0104',
                'is_active' => true,
            ],
            [
                'name' => 'Healthcare Plus',
                'description' => 'Medical facility safety management',
                'contact_email' => 'safety@healthcareplus.com',
                'contact_phone' => '+1-555-0105',
                'is_active' => true,
            ],
            [
                'name' => 'Mining Safety Corp',
                'description' => 'Mining industry HSE solutions',
                'contact_email' => 'hse@miningsafety.com',
                'contact_phone' => '+1-555-0106',
                'is_active' => true,
            ],
            [
                'name' => 'ChemSafe Industries',
                'description' => 'Chemical safety and compliance',
                'contact_email' => 'safety@chemsafe.com',
                'contact_phone' => '+1-555-0107',
                'is_active' => true,
            ],
            [
                'name' => 'Food Safety Systems',
                'description' => 'Food industry HSE management',
                'contact_email' => 'hse@foodsafety.com',
                'contact_phone' => '+1-555-0108',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
