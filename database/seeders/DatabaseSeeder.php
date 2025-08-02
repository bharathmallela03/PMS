<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Pharmacist;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Medicine;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Use firstOrCreate to prevent duplicate entries.
        // It checks for the first array's attributes, and if not found, creates a new record with the merged attributes of both arrays.
        Admin::firstOrCreate(
            ['email' => 'admin@pharmacy.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
            ]
        );

        // Create Sample Pharmacist
        Pharmacist::firstOrCreate(
            ['email' => 'pharmacist@test.com'],
            [
                'name' => 'Dr. John Doe',
                'shop_name' => 'City Medical Store',
                'contact_number' => '+91-9876543210',
                'address' => '123 Main Street',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'country' => 'India',
                'password' => Hash::make('password'),
                'password_setup_at' => now(),
                'is_active' => true,
            ]
        );

        // Create Sample Supplier
        Supplier::firstOrCreate(
            ['email' => 'supplier@test.com'],
            [
                'name' => 'Rajesh Kumar',
                'shop_name' => 'Pharma Distributors Pvt Ltd',
                'contact_number' => '+91-9876543211',
                'address' => '456 Industrial Area',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
                'country' => 'India',
                'password' => Hash::make('password'),
                'password_setup_at' => now(),
                'is_active' => true,
            ]
        );

        // Create Sample Customer
        Customer::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Priya Sharma',
                'contact_number' => '+91-9876543212',
                'address' => '789 Residential Colony',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode' => '560001',
                'country' => 'India',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Create Sample Companies
        $companies = [
            [
                'name' => 'Cipla Ltd',
                'email' => 'info@cipla.com',
                'phone' => '+91-22-2482-1234',
                'address' => 'Cipla House, Peninsula Business Park',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'website' => 'https://www.cipla.com',
                'description' => 'Leading pharmaceutical company',
                'is_active' => true,
            ],
            [
                'name' => 'Sun Pharmaceuticals',
                'email' => 'info@sunpharma.com',
                'phone' => '+91-22-4324-4324',
                'address' => 'Sun House, 201 B/1, Western Express Highway',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'website' => 'https://www.sunpharma.com',
                'description' => 'Largest pharmaceutical company in India',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Reddys Laboratories',
                'email' => 'info@drreddys.com',
                'phone' => '+91-40-4900-2900',
                'address' => '8-2-337, Road No. 3, Banjara Hills',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'country' => 'India',
                'website' => 'https://www.drreddys.com',
                'description' => 'Global pharmaceutical company',
                'is_active' => true,
            ],
            [
                'name' => 'Lupin Limited',
                'email' => 'info@lupin.com',
                'phone' => '+91-22-6640-2323',
                'address' => 'Kalina, Santacruz (East)',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'website' => 'https://www.lupin.com',
                'description' => 'Multinational pharmaceutical company',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $companyData) {
            // Use name as the unique identifier for companies
            Company::firstOrCreate(['name' => $companyData['name']], $companyData);
        }

        // Ensure the base users exist before trying to fetch them
        $pharmacist = Pharmacist::where('email', 'pharmacist@test.com')->first();
        $supplier = Supplier::where('email', 'supplier@test.com')->first();
        
        // Only seed medicines if the pharmacist and supplier were found
        if ($pharmacist && $supplier) {
            $medicines = [
                [
                    'name' => 'Paracetamol 500mg',
                    'brand' => 'Crocin',
                    'generic_name' => 'Acetaminophen',
                    'category' => 'Analgesic',
                    'description' => 'Pain relief and fever reducer',
                    'quantity' => 100,
                    'price' => 2.50,
                    'cost_price' => 2.00,
                    'expiry_date' => '2025-12-31',
                    'batch_number' => 'BATCH001',
                    'company_id' => Company::where('name', 'Cipla Ltd')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 50,
                    'is_active' => true,
                ],
                [
                    'name' => 'Amoxicillin 250mg',
                    'brand' => 'Amoxil',
                    'generic_name' => 'Amoxicillin',
                    'category' => 'Antibiotic',
                    'description' => 'Bacterial infection treatment',
                    'quantity' => 75,
                    'price' => 45.00,
                    'cost_price' => 35.00,
                    'expiry_date' => '2025-08-15',
                    'batch_number' => 'BATCH002',
                    'company_id' => Company::where('name', 'Sun Pharmaceuticals')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 30,
                    'is_active' => true,
                ],
                [
                    'name' => 'Cetirizine 10mg',
                    'brand' => 'Zyrtec',
                    'generic_name' => 'Cetirizine Hydrochloride',
                    'category' => 'Antihistamine',
                    'description' => 'Allergy relief medication',
                    'quantity' => 120,
                    'price' => 8.75,
                    'cost_price' => 6.50,
                    'expiry_date' => '2026-01-20',
                    'batch_number' => 'BATCH003',
                    'company_id' => Company::where('name', 'Dr. Reddys Laboratories')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 40,
                    'is_active' => true,
                ],
                [
                    'name' => 'Omeprazole 20mg',
                    'brand' => 'Prilosec',
                    'generic_name' => 'Omeprazole',
                    'category' => 'Antacid',
                    'description' => 'Acid reflux and heartburn treatment',
                    'quantity' => 45,
                    'price' => 12.30,
                    'cost_price' => 9.00,
                    'expiry_date' => '2025-09-10',
                    'batch_number' => 'BATCH004',
                    'company_id' => Company::where('name', 'Lupin Limited')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 50,
                    'is_active' => true,
                ],
                [
                    'name' => 'Vitamin D3 1000IU',
                    'brand' => 'VitaD',
                    'generic_name' => 'Cholecalciferol',
                    'category' => 'Vitamin',
                    'description' => 'Bone health supplement',
                    'quantity' => 30,
                    'price' => 25.00,
                    'cost_price' => 18.00,
                    'expiry_date' => '2026-03-15',
                    'batch_number' => 'BATCH005',
                    'company_id' => Company::where('name', 'Cipla Ltd')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 50,
                    'is_active' => true,
                ],
                [
                    'name' => 'Ibuprofen 400mg',
                    'brand' => 'Advil',
                    'generic_name' => 'Ibuprofen',
                    'category' => 'Analgesic',
                    'description' => 'Pain and inflammation relief',
                    'quantity' => 85,
                    'price' => 15.50,
                    'cost_price' => 12.00,
                    'expiry_date' => '2025-11-25',
                    'batch_number' => 'BATCH006',
                    'company_id' => Company::where('name', 'Sun Pharmaceuticals')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 40,
                    'is_active' => true,
                ],
                [
                    'name' => 'Metformin 500mg',
                    'brand' => 'Glucophage',
                    'generic_name' => 'Metformin Hydrochloride',
                    'category' => 'Antidiabetic',
                    'description' => 'Type 2 diabetes medication',
                    'quantity' => 65,
                    'price' => 18.75,
                    'cost_price' => 14.50,
                    'expiry_date' => '2025-10-30',
                    'batch_number' => 'BATCH007',
                    'company_id' => Company::where('name', 'Dr. Reddys Laboratories')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 35,
                    'is_active' => true,
                ],
                [
                    'name' => 'Dettol Antiseptic Liquid',
                    'brand' => 'Dettol',
                    'generic_name' => 'Chloroxylenol',
                    'category' => 'Antiseptic',
                    'description' => 'Antiseptic disinfectant',
                    'quantity' => 40,
                    'price' => 65.00,
                    'cost_price' => 50.00,
                    'expiry_date' => '2026-05-20',
                    'batch_number' => 'BATCH008',
                    'company_id' => Company::where('name', 'Lupin Limited')->first()->id,
                    'pharmacist_id' => $pharmacist->id,
                    'supplier_id' => $supplier->id,
                    'minimum_stock' => 50,
                    'is_active' => true,
                ],
            ];

            foreach ($medicines as $medicineData) {
                // Use a combination of name and batch_number as the unique identifier for medicines
                Medicine::firstOrCreate(
                    [
                        'name' => $medicineData['name'],
                        'batch_number' => $medicineData['batch_number']
                    ],
                    $medicineData
                );
            }
        }


        $this->command->info('Sample data has been seeded successfully!');
        $this->command->info('Default Admin Login: admin@pharmacy.com / password');
        $this->command->info('Default Pharmacist Login: pharmacist@test.com / password');
        $this->command->info('Default Supplier Login: supplier@test.com / password');
        $this->command->info('Default Customer Login: customer@test.com / password');
    }
}
