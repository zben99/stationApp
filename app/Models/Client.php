<?php
namespace App\Models;

use App\Models\CreditTopup;
use App\Models\BalanceTopup;
use App\Models\CreditPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

