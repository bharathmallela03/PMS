<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pharmacist extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'shop_name',
        'email',
        'contact_number',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'password',
        'is_active',
        'setup_token',
        'password_setup_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'setup_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_setup_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function needsPasswordSetup()
    {
        return is_null($this->password_setup_at);
    }
}