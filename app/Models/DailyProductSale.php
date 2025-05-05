<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProductSale extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $fillable = [
        'station_id',
        'product_packaging_id',
        'date',
        'rotation',
        'quantity',
        'unit_price',
        'total_price',
    ];

    // 🔁 Relation vers la station
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // 🔁 Relation vers le conditionnement du produit (packaging lié à la station)
    public function productPackaging()
    {
        return $this->belongsTo(ProductPackaging::class, 'product_packaging_id');
    }

    // 🔁 Accès simplifié au produit
    public function product()
    {
        return $this->productPackaging?->product;
    }

    // ✅ Montant total calculé dynamiquement (au cas où)
    public function getMontantCalculeAttribute()
    {
        return round($this->quantity * $this->unit_price, 2);
    }
}
