<?php

// MODELE : LubricantReception.php

namespace App\Models;

use App\Models\Supplier;
use App\Models\StationProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LubricantReception extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_product_id',
        'supplier_id',
        'date_reception',
        'quantite',
        'prix_achat',
        'prix_vente',
        'observations',
    ];

    public function product()
    {
        return $this->belongsTo(StationProduct::class, 'station_product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
