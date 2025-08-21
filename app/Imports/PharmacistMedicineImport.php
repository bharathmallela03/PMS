<?php

namespace App\Imports;

use App\Models\Medicine;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

// The class name is changed here
class PharmacistMedicineImport implements ToModel, WithHeadingRow, WithValidation
{
    private $pharmacist;
    private $companies;

    public function __construct()
    {
        $this->pharmacist = Auth::guard('pharmacist')->user();
        // Cache companies to avoid querying the DB for every single row
        $this->companies = Company::pluck('id', 'name')->all();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $companyName = $row['company_name'];
        $companyId = $this->companies[$companyName] ?? null;

        // If company doesn't exist, create it and add to our cached list
        if (!$companyId && !empty($companyName)) {
            $company = Company::create(['name' => $companyName, 'is_active' => true]);
            $this->companies[$companyName] = $company->id;
            $companyId = $company->id;
        }

        return new Medicine([
            'pharmacist_id'   => $this->pharmacist->id,
            'name'            => $row['name'],
            'brand'           => $row['brand'],
            'generic_name'    => $row['generic_name'] ?? null,
            'category'        => $row['category'],
            'description'     => $row['description'] ?? null,
            'quantity'        => $row['quantity'],
            'price'           => $row['price'],
            'cost_price'      => $row['cost_price'],
            'expiry_date'     => \Carbon\Carbon::parse($row['expiry_date']),
            'batch_number'    => $row['batch_number'],
            'company_id'      => $companyId,
            'minimum_stock'   => $row['minimum_stock'] ?? 10,
            'is_active'       => true,
        ]);
    }

    public function rules(): array
    {
        // Validation rules for each column in the Excel file
        return [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'expiry_date' => 'required|date', // Excel dates are stored as numbers
            'batch_number' => 'required|string',
            'company_name' => 'required|string',
        ];
    }
}