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
        Schema::create('tank_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->decimal('previous_quantity', 10, 2); // Stock avant modification
            $table->decimal('change_quantity', 10, 2);   // + ou - selon la réception ou la vente
            $table->decimal('new_quantity', 10, 2);      // Stock après modification
            $table->enum('operation_type', ['reception', 'vente', 'manuel']); // Type d'opération
            $table->unsignedBigInteger('operation_id')->nullable(); // Réf. ID FuelReception, FuelIndex, etc.
            $table->dateTime('operation_date'); // date réelle de l'opération
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tank_stock_histories');
    }
};
