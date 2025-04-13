<?php

namespace App\Models;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id', 'name', 'description', 'is_active',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // Une catégorie peut avoir plusieurs dépenses
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
