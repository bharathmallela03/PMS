<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bill_number',
        'patient_name',
        'patient_phone',
        'patient_address',
        'subtotal',
        'discount_percentage',
        'discount_amount',
        'total_amount',
        'status',
        'pharmacist_id',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the bill items for this bill
     */
    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Get the pharmacist who created this bill
     */
    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope for searching by patient name or bill number
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('patient_name', 'like', '%' . $search . '%')
              ->orWhere('bill_number', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'overdue' => 'bg-danger'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    /**
     * Check if bill is overdue (pending for more than 30 days)
     */
    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->created_at->diffInDays(now()) > 30;
    }
}