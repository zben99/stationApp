<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationCategory extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 'name', 'type', 'is_active'];

    public function stationProducts()
    {
        return $this->hasMany(StationProduct::class);
    }
}
