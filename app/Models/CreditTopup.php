<?php

namespace App\Models;

use App\Models\User;
use App\Models\Client;
use App\Models\Station;
use App\Models\CreditPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditTopup extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'client_id',
        'amount',
        'date',
        'notes',
        'created_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function getTotalPaymentsAttribute()
{
    return $this->payments()->sum('amount');
}

public function getRemainingBalanceAttribute()
{
    return $this->amount - $this->total_payments;
}

public function getStatusAttribute()
{
    if ($this->total_payments == 0) {
        return 'Non remboursé';
    } elseif ($this->total_payments < $this->amount) {
        return 'Partiellement remboursé';
    } else {
        return 'Totalement remboursé';
    }
}


}
