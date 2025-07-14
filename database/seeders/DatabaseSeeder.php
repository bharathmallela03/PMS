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
        // Create Admin
        Admin::create([
            'name' => 'System Administrator',
            'email' => 'admin@pharmacy.com',
            'password' => Hash::make('password'),
        ]);

        // Create Sample Pharmacist
        Pharmacist::create([
            'name' => 'Dr. John Doe',
            'shop_name' => 'City Medical Store',
            'email' => 'pharmacist@test.com',
            'contact_number' => '+91-9876543210',
            'address' => '123 Main Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'country' => 'India',
            'password' => Hash::make('password'),
            'password_setup_at' => now(),
            'is_active' => true,
        ]);

        // Create Sample Supplier
        Supplier::create([
            'name' => 'Rajesh Kumar',
            'shop_name' => 'Pharma Distributors Pvt Ltd',
            'email' => 'supplier@test.com',
            'contact_number' => '+91-9876543211',
            'address' => '456 Industrial Area',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'country' => 'India',
            'password' => Hash::make('password'),
            'password_setup_at' => now(),
            'is_active' => true,
        ]);

        // Create Sample Customer
        Customer::create([
            'name' => 'Priya Sharma',
            'email' => 'customer@test.com',
            'contact_number' => '+91-9876543212',
            'address' => '789 Residential Colony',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'pincode' => '560001',
            'country' => 'India',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

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

        foreach ($companies as $company) {
            Company::create($company);
        }

        // Create Sample Medicines
        $pharmacist = Pharmacist::first();
        $supplier = Supplier::first();
        
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
                'company_id' => 1,
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
                'company_id' => 2,
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
                'company_id' => 3,
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
                'company_id' => 4,
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
                'company_id' => 1,
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
                'company_id' => 2,
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
                'company_id' => 3,
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
                'company_id' => 4,
                'pharmacist_id' => $pharmacist->id,
                'supplier_id' => $supplier->id,
                'minimum_stock' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }

        $this->command->info('Sample data has been seeded successfully!');
        $this->command->info('Default Admin Login: admin@pharmacy.com / password');
        $this->command->info('Default Pharmacist Login: pharmacist@test.com / password');
        $this->command->info('Default Supplier Login: supplier@test.com / password');
        $this->command->info('Default Customer Login: customer@test.com / password');
    }
}