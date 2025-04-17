<?php
namespace App\Models;

use App\Models\Driver;
use App\Models\Station;
use App\Models\Transporter;
use App\Models\FuelReceptionLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelReception extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'date_reception',
        'num_bl',
        'transporter_id',
        'driver_id',
        'contre_plein_litre',
        'contre_plein_valeur',
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
