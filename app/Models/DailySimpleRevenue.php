<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySimpleRevenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'date',
        'rotation',
        'type',
        'amount',
        'created_by',
    ];

    // Station liée
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // Utilisateur qui a créé l’enregistrement
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🔁 Méthode pratique : libellé lisible du type
    public function getLabelAttribute()
    {
        return $this->type === 'boutique' ? 'Recette Boutique' : 'Recette Lavage';
    }
}
