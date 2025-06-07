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
        Schema::create('packagings', function (Blueprint $table) {
            $table->id();
              $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->string('label'); // Ex : "1L", "5L", "20L", "Fût", etc.
             $table->string('type')->nullable(); // Ex : lubrifiant, gaz, lavage
            $table->decimal('quantity', 8, 2)->nullable();; // Ex : 1.00, 5.00, etc.
             $table->string('unit')->default('L')->nullable(); // Exemple : L, kg, unité
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packagings');
    }
};
