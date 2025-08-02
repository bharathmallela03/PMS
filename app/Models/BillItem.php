<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'pharmacist_id',
        'medicine_id',
        'quantity',
        'price',
        'total',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
