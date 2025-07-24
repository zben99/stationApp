<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fuel_stock_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->date('control_date');
            $table->decimal('stock_opening', 10, 2)->default(0);
            $table->decimal('index_start', 15, 2)->default(0);
            $table->decimal('index_end', 15, 2)->default(0);
            $table->decimal('return_to_tank', 10, 2)->default(0);
            $table->decimal('sale', 10, 2)->default(0);
            $table->decimal('reception', 10, 2)->default(0);
            $table->decimal('stock_theoretical', 10, 2)->default(0);
            $table->decimal('stock_physical', 10, 2)->default(0);
            $table->decimal('gap_liters', 10, 2)->default(0);
            $table->decimal('gap_percent', 7, 2)->default(0);
            $table->timestamps();

            $table->unique(['tank_id', 'control_date']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_stock_controls');
    }
};
