<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'name',
        'phone',
        'email',
        'address',
    ];

    public function receptions()
    {
        return $this->hasMany(FuelReception::class);
    }
}
