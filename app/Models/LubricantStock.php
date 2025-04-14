<?php


// MODELE : LubricantStock.php

namespace App\Models;

use App\Models\StationProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LubricantStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_product_id',
        'quantite_actuelle',
    ];

    public function product()
    {
        return $this->belongsTo(StationProduct::class, 'station_product_id');
    }
}
