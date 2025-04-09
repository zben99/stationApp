<?php

namespace App\Models;

use App\Models\Tank;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelReception extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'date_reception',
        'quantite_livree',
        'densite',
        'supplier_id',
        'num_bl',
        'remarques',
    ];


    protected $casts = [
        'date_reception' => 'date',
    ];


    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    // Accès direct au produit via la cuve
    public function product()
    {
        return $this->tank?->product();
    }

    public function station()
    {
        return $this->tank?->station();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}

