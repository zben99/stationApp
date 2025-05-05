<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRevenueReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'date',
        'rotation',
        'fuel_amount',
        'product_amount',
        'shop_amount',
        'total_amount',
    ];

    /**
     * La station associée à cette revue.
     */
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * Accesseur pour afficher la date formatée (facultatif).
     */
    public function getFormattedDateAttribute()
    {
        return \Carbon\Carbon::parse($this->date)->format('d/m/Y');
    }
}
