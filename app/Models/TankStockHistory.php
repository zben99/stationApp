<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TankStockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'tank_id',
        'previous_quantity',
        'change_quantity',
        'new_quantity',
        'operation_type',
        'operation_id',
        'operation_date',
    ];

    protected $casts = [
        'previous_quantity' => 'float',
        'change_quantity' => 'float',
        'new_quantity' => 'float',
        'operation_date' => 'datetime',
    ];

    // 🔁 Relations utiles
    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // Si tu veux relier au modèle FuelIndex ou FuelReception dynamiquement
    public function operation()
    {
        if ($this->operation_type === 'vente') {
            return $this->belongsTo(FuelIndex::class, 'operation_id');
        }

        if ($this->operation_type === 'reception') {
            return $this->belongsTo(FuelReception::class, 'operation_id');
        }

        return null;
    }
}
