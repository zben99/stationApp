<?php

namespace App\Models;

use App\Models\Tank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
