<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sponsor;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sponsors = [
            [
                'sponsor_id' => 'SPO2023001',
                'company_name' => 'ABC Corporation',
                'contact_person' => 'Robert Brown',
                'email' => 'robert.brown@abc-corp.com',
                'phone' => '0777890123',
                'whatsapp_number' => '0777890123',
                'address' => '123 Corporate Ave, Colombo 3',
                'sponsorship_type' => 'main',
                'sponsorship_amount' => 1000000.00,
                'sponsorship_start_date' => '2023-01-01',
                'sponsorship_end_date' => '2023-12-31',
                'contract_start_date' => '2023-01-01',
                'contract_end_date' => '2023-12-31',
                'status' => 'active',
            ],
            [
                'sponsor_id' => 'SPO2023002',
                'company_name' => 'XYZ Industries',
                'contact_person' => 'Mary White',
                'email' => 'mary.white@xyz-industries.com',
                'phone' => '0778901234',
                'whatsapp_number' => '0778901234',
                'address' => '456 Industry Rd, Colombo 10',
                'sponsorship_type' => 'co',
                'sponsorship_amount' => 500000.00,
                'sponsorship_start_date' => '2023-01-01',
                'sponsorship_end_date' => '2023-12-31',
                'contract_start_date' => '2023-01-01',
                'contract_end_date' => '2023-12-31',
                'status' => 'active',
            ],
        ];

        foreach ($sponsors as $sponsorData) {
            Sponsor::create($sponsorData);
        }
    }
}
