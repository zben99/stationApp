<?php

namespace App\Models;

use App\Models\Station;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'station_id', 'stock', 'price'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
