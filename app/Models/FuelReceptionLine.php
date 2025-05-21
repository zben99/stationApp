<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'contre_plein_litre',
        'contre_plein_valeur',
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
