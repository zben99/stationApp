<?php

namespace App\Models;

use App\Models\Tank;
use App\Models\User;
use App\Models\Product;
use App\Models\StationCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(StationCategory::class);
    }


    public function tanks()
{
    return $this->hasMany(Tank::class);
}
}
