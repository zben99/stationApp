<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRevenueValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'date',
        'rotation',
        'fuel_amount',
        'product_amount',
        'shop_amount',
        'om_amount',
        'tpe_amount',
        'cash_amount',
        'credit_received',
        'credit_repaid',
        'balance_received',
        'balance_used',
        'expenses',
        'net_to_deposit',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'date' => 'date',
        'validated_at' => 'datetime',
        'fuel_amount' => 'decimal:2',
        'product_amount' => 'decimal:2',
        'shop_amount' => 'decimal:2',
        'om_amount' => 'decimal:2',
        'tpe_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'credit_received' => 'decimal:2',
        'credit_repaid' => 'decimal:2',
        'balance_received' => 'decimal:2',
        'balance_used' => 'decimal:2',
        'expenses' => 'decimal:2',
        'net_to_deposit' => 'decimal:2',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    public function getRotationLabelAttribute()
    {
        return match ($this->rotation) {
            '6-14' => '6h - 14h',
            '14-22' => '14h - 22h',
            '22-6' => '22h - 6h',
            default => $this->rotation,
        };
    }
}
