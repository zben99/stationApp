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
        'credit_balance',
        'is_active',
        'notes',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}

