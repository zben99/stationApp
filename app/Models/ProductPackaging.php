<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPackaging extends Model
{
    protected $table = 'station_product_packaging';

    protected $fillable = [
        'station_product_id',
        'packaging_id',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(StationProduct::class, 'station_product_id');
    }

    public function packaging()
    {
        return $this->belongsTo(Packaging::class);
    }
}

