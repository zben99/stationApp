<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LubricantStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_product_id',
        'product_packaging_id',
        'quantite_actuelle',
    ];

    public function stationProduct()
    {
        return $this->belongsTo(StationProduct::class);
    }

    public function productPackaging()
    {
        return $this->belongsTo(ProductPackaging::class);
    }



}
