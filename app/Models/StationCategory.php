<?php

namespace App\Models;

use App\Models\StationProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StationCategory extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 'name', 'type', 'is_active'];

    public function stationProducts()
    {
        return $this->hasMany(StationProduct::class);
    }
}
