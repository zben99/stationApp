<?php

namespace App\Traits;

use App\Models\DailyRevenueValidation;
use App\Models\ModificationLog;

trait ValidatesRotation
{
    protected function modificationAllowed(int $stationId, $date, $rotation): bool
    {
        $isValidated = DailyRevenueValidation::where('station_id', $stationId)
            ->where('date', $date)
            ->where('rotation', $rotation)
            ->exists();

        $user = auth()->user();
        $canOverride = $user && $user->hasAnyRole([
            'Super Gestionnaire',
            'Gestionnaire Multi-Sites',
            'Gestionnaire de Site Unique',
        ]);

        return ! $isValidated || $canOverride;
    }

    protected function logOverride($model, array $before, array $after): void
    {
        ModificationLog::create([
            'user_id' => auth()->id(),
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => [
                'before' => $before,
                'after' => $after,
            ],
        ]);
    }
}
