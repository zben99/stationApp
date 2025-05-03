<?php

namespace App\Models;

use App\Models\Station;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'expense_category_id',
        'date_depense',
        'rotation',
        'description',
        'montant',
        'piece_jointe',
    ];

    protected $casts = [
        'date_depense' => 'date',
    ];

    // Une dépense appartient à une station
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // Une dépense appartient à une catégorie
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
