<?php
namespace App\Models;

use App\Models\User;
use App\Models\Client;
use App\Models\Station;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BalanceUsage extends Model
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
}
