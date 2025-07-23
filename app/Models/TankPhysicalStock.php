<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TankPhysicalStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'tank_id',
        'date',
        'quantity',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
