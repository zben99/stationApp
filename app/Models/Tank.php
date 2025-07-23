<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pump;
use App\Models\Station;
use App\Models\StationProduct;
use App\Models\TankStock;
use App\Models\FuelReception;
use App\Models\TankPhysicalStock;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'station_product_id',
        'code',
        'capacite',
    ];

    protected static function booted()
    {
        static::deleting(function ($tank) {
            $tank->stock()->delete();
        });
    }

    // Une cuve appartient à une station
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // Une cuve est liée à un produit de type carburant
    public function product()
    {
        return $this->belongsTo(StationProduct::class, 'station_product_id');
    }

    // Une cuve a un stock
    public function stock()
    {
        return $this->hasOne(TankStock::class);
    }

    // Une cuve peut recevoir plusieurs réceptions
    public function receptions()
    {
        return $this->hasMany(FuelReception::class);
    }

    // Une cuve alimente plusieurs pompes
    public function pumps()
    {
        return $this->hasMany(Pump::class);
    }

    public function physicalStocks()
    {
        return $this->hasMany(TankPhysicalStock::class);
    }
}
