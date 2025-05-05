<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'name',
        'phone',
        'email',
        'address',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
