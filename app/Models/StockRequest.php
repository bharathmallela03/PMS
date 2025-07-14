<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_FULFILLED = 'fulfilled';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'pharmacist_id',
        'supplier_id',
        'medicine_id',
        'requested_quantity',
        'fulfilled_quantity',
        'status',
        'notes',
        'pharmacist_notes',
        'supplier_notes',
        'fulfilled_at',
    ];

    protected $casts = [
        'fulfilled_at' => 'datetime',
    ];

    public function pharmacist()
    {
        return $this->belongsTo(Pharmacist::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_FULFILLED => 'success',
            self::STATUS_REJECTED => 'danger',
        ][$this->status] ?? 'secondary';
    }

    public function canBeFulfilled()
    {
        return $this->status === self::STATUS_PENDING || $this->status === self::STATUS_APPROVED;
    }

    public function scopeByPharmacist($query, $pharmacistId)
    {
        return $query->where('pharmacist_id', $pharmacistId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}