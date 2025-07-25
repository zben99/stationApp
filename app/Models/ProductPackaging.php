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
        'prix_achat',
    ];

    public function product()
    {
        return $this->belongsTo(StationProduct::class, 'station_product_id');
    }

    public function packaging()
    {
        return $this->belongsTo(Packaging::class);
    }

    public function lubricantStock()
    {
        return $this->hasOne(LubricantStock::class, 'product_packaging_id');
    }

    public function getQuantiteDisponibleAttribute()
    {
        if ($this->lubricantStock && $this->lubricantStock->product_packaging_id == $this->id) {
            return $this->lubricantStock->quantite_actuelle;
        }

        return 0;
    }
}
