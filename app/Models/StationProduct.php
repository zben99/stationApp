<?php

namespace App\Models;

use App\Models\Tank;
use App\Models\Station;
use App\Models\Packaging;
use App\Models\StationCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StationProduct extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 'name', 'category_id', 'price'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function stationCategory()
    {
        return $this->belongsTo(StationCategory::class, 'category_id');
    }

    public function tanks()
    {
        return $this->hasMany(Tank::class);
    }

    public function packagings()
    {
        return $this->belongsToMany(Packaging::class, 'station_product_packaging')
                    ->withPivot('price')
                    ->withTimestamps();
    }


    public function productPackagings()
    {
        return $this->hasMany(ProductPackaging::class, 'station_product_id', 'id');
    }

}
