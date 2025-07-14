<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'name',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFullAddressAttribute()
    {
        $address = $this->address_line_1;
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        $address .= ', ' . $this->city . ', ' . $this->state . ' - ' . $this->pincode;
        if ($this->country) {
            $address .= ', ' . $this->country;
        }
        return $address;
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            if ($address->is_default) {
                static::where('customer_id', $address->customer_id)->update(['is_default' => false]);
            }
        });

        static::updating(function ($address) {
            if ($address->is_default) {
                static::where('customer_id', $address->customer_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}