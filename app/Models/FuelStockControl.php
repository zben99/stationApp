<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelStockControl extends Model
{
    protected $fillable = [
        'station_id',
        'tank_id',
        'control_date',
        'stock_opening',
        'index_start',
        'index_end',
        'return_to_tank',
        'sale',
        'reception',
        'stock_theoretical',
        'stock_physical',
        'gap_liters',
        'gap_percent',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
