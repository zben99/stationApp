<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyRevenueValidation extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 'date', 'rotation', 'validated_by', 'validated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
