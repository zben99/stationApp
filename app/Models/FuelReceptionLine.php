<?php

namespace App\Models;

use App\Models\Tank;
use App\Models\FuelReception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelReceptionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_reception_id',
        'tank_id',
        'jauge_avant',
        'reception_par_cuve',
        'jauge_apres',
        'ecart_reception',
        'ecart_stock',
        'ecart',
        'sonabhy_d',
        'sonabhy_t',
        'sonabhy_d15',
        'station_d',
        'station_t',
        'station_d15',
    ];

    public function reception()
    {
        return $this->belongsTo(FuelReception::class, 'fuel_reception_id');
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
