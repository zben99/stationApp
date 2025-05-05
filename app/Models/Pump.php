<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pump extends Model
{
    protected $fillable = ['station_id', 'tank_id', 'name'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
