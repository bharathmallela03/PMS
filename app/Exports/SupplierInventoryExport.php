<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierInventoryExport implements FromQuery, WithHeadings, WithMapping
{
    protected $supplierId;

    public function __construct(int $supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        // Fetch all medicines belonging to the specified supplier
        return Medicine::query()
            ->where('supplier_id', $this->supplierId)
            ->with('company');
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // These will be the column headers in the Excel file
        return [
            'ID',
            'Name',
            'Brand',
            'Generic Name',
            'Category',
            'Price',
            'Company',
            'Created At',
        ];
    }

    /**
    * @param mixed $medicine
    * @return array
    */
    public function map($medicine): array
    {
        // This maps the data from each medicine model to a row in the Excel file
        return [
            $medicine->id,
            $medicine->name,
            $medicine->brand,
            $medicine->generic_name,
            $medicine->category,
            $medicine->price,
            $medicine->company->name ?? 'N/A', // Safely access company name
            $medicine->created_at->toDateTimeString(),
        ];
    }
}
