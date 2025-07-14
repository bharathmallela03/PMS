<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getCartCount()
    {
        return $this->cart()->sum('quantity');
    }

    public function getCartTotal()
    {
        return $this->cart()->with('medicine')->get()->sum(function ($item) {
            return $item->quantity * $item->medicine->price;
        });
    }
}