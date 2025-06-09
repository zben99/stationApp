<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRevenueValidation extends Model
{
    use HasFactory;

    /* ===== COLONNES INSÉRABLES / MODIFIABLES ====================== */
    protected $fillable = [
        'station_id',
        'date',
        'rotation',

        /* Carburants */
        'fuel_super_amount',
        'fuel_gazoil_amount',

        /* Produits par famille */
        'lub_amount',
        'pea_amount',
        'gaz_amount',
        'lampes_amount',
        'lavage_amount',
        'boutique_amount',

        /* Crédits & avoirs */
        'credit_received',
        'credit_repaid',
        'balance_received',
        'balance_used',

        /* Mouvements électroniques */
        'tpe_amount',
        'tpe_recharge_amount',
        'om_amount',

        /* Dépenses & liquidités */
        'expenses',
        'cash_amount',
        'net_to_deposit',

        /* Validation */
        'validated_by',
        'validated_at',
    ];

    /* ===== CASTS (décimaux 2 chiffres) ============================ */
    protected $casts = [
        'date'          => 'date',
        'validated_at'  => 'datetime',

        /* Carburants */
        'fuel_super_amount'   => 'decimal:2',
        'fuel_gazoil_amount'  => 'decimal:2',

        /* Produits */
        'lub_amount'      => 'decimal:2',
        'pea_amount'      => 'decimal:2',
        'gaz_amount'      => 'decimal:2',
        'lampes_amount'   => 'decimal:2',
        'lavage_amount'   => 'decimal:2',
        'boutique_amount' => 'decimal:2',

        /* Crédits / Avoirs */
        'credit_received'   => 'decimal:2',
        'credit_repaid'     => 'decimal:2',
        'balance_received'  => 'decimal:2',
        'balance_used'      => 'decimal:2',

        /* Électronique + autres */
        'tpe_amount'   => 'decimal:2',
        'tpe_recharge_amount'   => 'decimal:2',
        'om_amount'    => 'decimal:2',
        'expenses'     => 'decimal:2',
        'cash_amount'  => 'decimal:2',
        'net_to_deposit' => 'decimal:2',
    ];

    /* ===== RELATIONS ============================================= */
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /* ===== ACCESSORS / HELPERS =================================== */
    public function getFormattedDateAttribute()
    {
        return $this->date?->format('d/m/Y');
    }

    public function getRotationLabelAttribute()
    {
        return match ($this->rotation) {
            '6-14'  => '6h - 14h',
            '14-22' => '14h - 22h',
            '22-6'  => '22h - 6h',
            default => $this->rotation,
        };
    }

    /* -------- Totaux calculés (pratique pour les vues) ---------- */
    public function getFuelAmountAttribute()
    {
        return ($this->fuel_super_amount ?? 0) + ($this->fuel_gazoil_amount ?? 0);
    }

    public function getProductAmountAttribute()
    {
        return
            ($this->lub_amount      ?? 0) +
            ($this->pea_amount      ?? 0) +
            ($this->gaz_amount      ?? 0) +
            ($this->lampes_amount   ?? 0) +
            ($this->lavage_amount   ?? 0) +
            ($this->boutique_amount ?? 0);
    }

    public function getTotalEncaissementAttribute()
    {
        return $this->fuel_amount
             + $this->product_amount
             + ($this->credit_repaid    ?? 0)
             + ($this->balance_received ?? 0);
    }

    public function getTotalDecaissementAttribute()
    {
        return ($this->expenses        ?? 0)
             + ($this->credit_received ?? 0)
             + ($this->balance_used    ?? 0);
    }

    public function getElectronicAmountAttribute()
    {
        return ($this->tpe_amount ?? 0) + ($this->om_amount ?? 0);
    }
}
