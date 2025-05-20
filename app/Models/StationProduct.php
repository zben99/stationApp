<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationProduct extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 'name', 'category_id', 'code', 'price'];

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
