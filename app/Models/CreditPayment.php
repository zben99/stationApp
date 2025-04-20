<?php

namespace App\Models;

use App\Models\User;
use App\Models\Client;
use App\Models\Station;
use App\Models\CreditTopup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'client_id',
        'credit_topup_id',
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

    public function creditTopup()
    {
        return $this->belongsTo(CreditTopup::class);
    }
}

