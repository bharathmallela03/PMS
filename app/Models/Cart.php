<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'medicine_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function getTotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->medicine->price;
    }
}