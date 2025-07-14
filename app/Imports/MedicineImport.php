<?php

namespace App\Imports;

use App\Models\Medicine;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class MedicineImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $supplierId;

    public function __construct($supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find or create company
        $company = Company::firstOrCreate(
            ['name' => $row['company_name']],
            ['is_active' => true]
        );

        return new Medicine([
            'name' => $row['name'],
            'brand' => $row['brand'],
            'generic_name' => $row['generic_name'] ?? null,
            'category' => $row['category'],
            'description' => $row['description'] ?? null,
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'cost_price' => $row['cost_price'],
            'expiry_date' => Carbon::parse($row['expiry_date']),
            'batch_number' => $row['batch_number'],
            'minimum_stock' => $row['minimum_stock'] ?? 50,
            'company_id' => $company->id,
            'supplier_id' => $this->supplierId,
            'is_active' => true,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'expiry_date' => 'required|date|after:today',
            'batch_number' => 'required|string|max:100',
            'company_name' => 'required|string|max:255',
        ];
    }
}