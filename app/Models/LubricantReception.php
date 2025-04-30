<?php

namespace App\Models;

use App\Models\Supplier;
use App\Models\StationProduct;
use App\Models\ProductPackaging;
use App\Models\LubricantStock; // tu avais oublié d'importer ce modèle ici
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LubricantReception extends Model
{
    use HasFactory;

    protected $casts = [
        'date_reception' => 'datetime',
    ];
    protected $fillable = [
        'station_product_id',
        'batch_id',
        'product_packaging_id',
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

    public function packaging()
    {
        return $this->belongsTo(ProductPackaging::class, 'product_packaging_id');
    }

    // ✅ Méthode pour mettre à jour ou créer le stock
    public static function updateOrCreateStock($stationProductId, $productPackagingId, $quantite, $previousQuantity = null)
    {
        // Cherche ou crée un stock
        $stock = LubricantStock::firstOrNew([
            'station_product_id' => $stationProductId,
            'product_packaging_id' => $productPackagingId,
        ]);

        // Si une quantité précédente existe, on l'enlève avant d'ajouter la nouvelle quantité
        if ($previousQuantity !== null) {
            $stock->quantite_actuelle -= $previousQuantity; // Enlever la quantité précédente
        }

        // Ajouter la nouvelle quantité reçue
        $stock->quantite_actuelle += $quantite; // Ajouter la nouvelle quantité

        // Sauvegarder les changements
        $stock->save();
    }

}
