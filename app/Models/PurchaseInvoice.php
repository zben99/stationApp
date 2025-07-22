<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'invoice_number',
        'date',
        'rotation',
        'supplier_name',
        'amount_ht',
        'amount_ttc',
        'created_by',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function updateAmounts()
    {
        // Calcule le montant payé jusqu'à présent
        $this->amount_paid = $this->payments->sum('amount');

        // Calcule le montant restant
        $this->amount_remaining = $this->amount_ttc - $this->amount_paid;

        // Sauvegarder les mises à jour
        $this->save();
    }

}
