<?php

namespace App\Imports;

use App\Models\Medicine;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class MedicinesImport implements ToModel, WithHeadingRow, WithValidation
{
    private $supplierId;

    public function __construct(int $supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find or create the company
        $company = Company::firstOrCreate(
            ['name' => $row['company']],
            ['is_active' => true]
        );

        return new Medicine([
            'name'         => $row['name'],
            'brand'        => $row['brand'],
            'generic_name' => $row['generic_name'],
            'category'     => $row['category'],
            'price'        => $row['price'],
            'company_id'   => $company->id,
            'supplier_id'  => $this->supplierId,
            'description'  => $row['description'] ?? null,
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
            'category' => 'required|string',
            'price' => 'required|numeric',
            'company' => 'required|string',
        ];
    }
}
