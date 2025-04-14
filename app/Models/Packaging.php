<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'volume_litre',
    ];

    public function products()
    {
        return $this->belongsToMany(StationProduct::class, 'station_product_packaging')
                    ->withPivot('price')
                    ->withTimestamps();
    }
}
