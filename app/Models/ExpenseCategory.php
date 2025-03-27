<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'station_id', 'name', 'description', 'is_active',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
