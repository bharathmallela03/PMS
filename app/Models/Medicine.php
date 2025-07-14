<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'brand',
        'generic_name',
        'category',
        'description',
        'quantity',
        'price',
        'cost_price',
        'expiry_date',
        'batch_number',
        'photo',
        'company_id',
        'pharmacist_id',
        'supplier_id',
        'is_active',
        'minimum_stock',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(Pharmacist::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/medicines/' . $this->photo);
        }
        return asset('images/medicine-placeholder.png');
    }

    public function isLowStock()
    {
        return $this->quantity <= ($this->minimum_stock ?? 50);
    }

    public function isOutOfStock()
    {
        return $this->quantity <= 0;
    }

    public function isExpired()
    {
        return $this->expiry_date < now();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date <= now()->addDays($days);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= COALESCE(minimum_stock, 50)');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeByPharmacist($query, $pharmacistId)
    {
        return $query->where('pharmacist_id', $pharmacistId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }
}