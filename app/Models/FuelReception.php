<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelReception extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'date_reception',
        'rotation',
        'num_bl',
        'transporter_id',
        'driver_id',
        'vehicle_registration',
        'observation_type',
        'observation_litre',
        'remarques',
    ];

    protected $casts = [
        'date_reception' => 'date',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public function lines()
    {
        return $this->hasMany(FuelReceptionLine::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
