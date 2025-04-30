<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LubricantReceptionBatch extends Model
{
    use HasFactory;

    protected $casts = [
        'date_reception' => 'datetime',
    ];

    protected $fillable = [
        'station_id',
        'supplier_id',
        'date_reception',
        'num_bc',
        'num_bl',
    ];

    // 🔁 Une batch a plusieurs lignes de réception
    public function receptions()
    {
        return $this->hasMany(LubricantReception::class, 'batch_id');
    }

    // 🔁 Lien vers la station concernée
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // 🔁 Lien vers le fournisseur (optionnel)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
