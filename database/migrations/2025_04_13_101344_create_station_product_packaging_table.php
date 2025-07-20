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
        Schema::create('station_product_packaging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('packaging_id')->constrained()->onDelete('cascade');
             $table->decimal('prix_achat', 10, 2)->nullable();  // Prix d'achat
            $table->decimal('price', 10, 2)->nullable(); // Prix de vente pour ce conditionnement
            $table->timestamps();

            $table->unique(['station_product_id', 'packaging_id']); // Unicité du couple produit + packaging
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_product_packaging');
    }
};
