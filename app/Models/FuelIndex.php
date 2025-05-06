<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelIndex extends Model
{
    use HasFactory;

    protected $table = 'fuel_indexes';

    protected $fillable = [
        'station_id',
        'pump_id',
        'user_id',
        'date',
        'rotation',
        'index_debut',
        'index_fin',
        'retour_en_cuve',
        'prix_unitaire',
        'montant_recette',
    ];

    protected $casts = [
        'date' => 'date',
        'rotation' => 'string',
        'index_debut' => 'decimal:2',
        'index_fin' => 'decimal:2',
        'retour_en_cuve' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'montant_recette' => 'decimal:2',
    ];

    // Relations
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Attributs calculés
    public function getQuantiteVendueAttribute()
    {
        return round($this->index_fin - $this->index_debut, 2);
    }

    public function getMontantTotalAttribute()
    {
        return round($this->quantite_vendue * $this->prix_unitaire, 2);
    }

    public function getEcartAttribute()
    {
        return round(($this->montant_declare ?? 0) - $this->montant_total, 2);
    }
}
