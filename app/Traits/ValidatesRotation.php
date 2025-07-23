<?php

namespace App\Traits;

use App\Models\DailyRevenueValidation;

trait ValidatesRotation
{
    protected function modificationAllowed(int $stationId, $date, $rotation): bool
    {
        return ! DailyRevenueValidation::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->exists();
    }
}
