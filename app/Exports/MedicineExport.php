<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MedicineExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $userId;
    protected $userType;

    public function __construct($userId, $userType = 'pharmacist')
    {
        $this->userId = $userId;
        $this->userType = $userType;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Medicine::with(['company', 'pharmacist', 'supplier']);

        if ($this->userType === 'pharmacist') {
            $query->where('pharmacist_id', $this->userId);
        } elseif ($this->userType === 'supplier') {
            $query->where('supplier_id', $this->userId);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Brand',
            'Generic Name',
            'Category',
            'Quantity',
            'Price',
            'Cost Price',
            'Expiry Date',
            'Batch Number',
            'Company',
            'Status',
            'Created Date',
        ];
    }

    /**
     * @param Medicine $medicine
     * @return array
     */
    public function map($medicine): array
    {
        return [
            $medicine->id,
            $medicine->name,
            $medicine->brand,
            $medicine->generic_name,
            $medicine->category,
            $medicine->quantity,
            $medicine->price,
            $medicine->cost_price,
            $medicine->expiry_date->format('Y-m-d'),
            $medicine->batch_number,
            $medicine->company->name ?? 'N/A',
            $medicine->is_active ? 'Active' : 'Inactive',
            $medicine->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}