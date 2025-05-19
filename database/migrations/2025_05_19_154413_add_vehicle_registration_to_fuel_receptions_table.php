<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuel_receptions', function (Blueprint $table) {
            $table->string('vehicle_registration', 30)
                  ->after('driver_id')
                  ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fuel_receptions', function (Blueprint $table) {
            $table->dropColumn('vehicle_registration');
        });
    }
};
