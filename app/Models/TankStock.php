<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TankStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'quantite_actuelle',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
