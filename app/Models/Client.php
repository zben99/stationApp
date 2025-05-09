<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'name',
        'phone',
        'email',
        'address',
        'is_active',
        'notes',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function creditTopups()
    {
        return $this->hasMany(CreditTopup::class);
    }

    public function balanceTopups()
    {
        return $this->hasMany(BalanceTopup::class);
    }

    public function creditPayments()
    {
        return $this->hasMany(CreditPayment::class);
    }

    public function getCreditBalanceAttribute()
    {
        return $this->creditTopups()->sum('amount') - $this->creditPayments()->sum('amount');
    }

    public function balanceUsages()
    {
        return $this->hasMany(BalanceUsage::class);
    }
}
