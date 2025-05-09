<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'name',
        'phone',
        'permis',
    ];

    public function receptions()
    {
        return $this->hasMany(FuelReception::class);
    }
}
