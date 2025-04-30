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
        Schema::create('lubricant_receptions', function (Blueprint $table) {
            $table->id();

            // Groupe de réception (batch)
            $table->foreignId('batch_id')
                  ->nullable()
                  ->constrained('lubricant_reception_batches')
                  ->onDelete('cascade');

            // Produit spécifique lié à une station
            $table->foreignId('station_product_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Conditionnement du produit
            $table->foreignId('product_packaging_id')
                  ->constrained('station_product_packaging')
                  ->onDelete('cascade');

            // Fournisseur
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');

            $table->date('date_reception');

            // Quantité et prix
            $table->decimal('quantite', 10, 2);
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->decimal('prix_vente', 10, 2)->nullable();

            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lubricant_receptions');
    }
};
