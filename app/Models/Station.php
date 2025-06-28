<?php

namespace App\Models;

use App\Models\StationProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'is_active'];

    public function products()
    {
        return $this->hasMany(StationProduct::class);
    }

    public function categories()
    {
        return $this->hasMany(StationCategory::class);
    }

    public function tanks()
    {
        return $this->hasMany(Tank::class);
    }

    public function packagings()
    {
        return $this->hasMany(Packaging::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
