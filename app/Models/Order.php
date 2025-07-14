<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'customer_id',
        'pharmacist_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'notes',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'billing_address',
        'shipping_address',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'billing_address' => 'array',
        'shipping_address' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(Pharmacist::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_SHIPPED => 'secondary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
        ][$this->status] ?? 'secondary';
    }

    public function getPaymentStatusColorAttribute()
    {
        return [
            self::PAYMENT_PENDING => 'warning',
            self::PAYMENT_PAID => 'success',
            self::PAYMENT_FAILED => 'danger',
            self::PAYMENT_REFUNDED => 'info',
        ][$this->payment_status] ?? 'secondary';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByPharmacist($query, $pharmacistId)
    {
        return $query->where('pharmacist_id', $pharmacistId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }
}