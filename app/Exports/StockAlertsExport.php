<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockAlertsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $pharmacistId;
    protected $medicineIds;

    public function __construct(int $pharmacistId, array $medicineIds = [])
    {
        $this->pharmacistId = $pharmacistId;
        $this->medicineIds = array_filter($medicineIds); // Remove empty values
    }

    public function collection()
    {
        $query = Medicine::where('pharmacist_id', $this->pharmacistId)
                         ->where(function ($q) {
                             $q->where('quantity', 0)
                               ->orWhereColumn('quantity', '<=', 'minimum_stock');
                         });

        if (!empty($this->medicineIds)) {
            $query->whereIn('id', $this->medicineIds);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Medicine Name',
            'SKU/Batch No.',
            'Category',
            'Current Stock',
            'Minimum Stock',
            'Alert Level',
            'Expiry Date',
        ];
    }

    public function map($medicine): array
    {
        if ($medicine->quantity == 0) {
            $level = 'Out of Stock';
        } elseif ($medicine->quantity <= ($medicine->minimum_stock * 0.5)) {
            $level = 'Critical';
        } else {
            $level = 'Low Stock';
        }

        return [
            $medicine->id,
            $medicine->name,
            $medicine->batch_number,
            $medicine->category,
            $medicine->quantity,
            $medicine->minimum_stock,
            $level,
            $medicine->expiry_date->format('Y-m-d'),
        ];
    }
}