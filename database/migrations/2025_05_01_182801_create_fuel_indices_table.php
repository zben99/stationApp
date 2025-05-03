<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fuel_indexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('pump_id')->constrained()->onDelete('cascade');

            $table->date('date'); // Date du relevé
            $table->enum('rotation', ['6-14', '14-22', '22-6']); // Rotation

            $table->decimal('index_debut', 10, 2);
            $table->decimal('index_fin', 10, 2);
            $table->decimal('montant_declare', 10, 2)->nullable(); // recette réellement encaissée
            $table->decimal('prix_unitaire', 10, 2); // Prix de vente carburant

            $table->decimal('retour_en_cuve', 10, 2)->nullable(); // quantité remise
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // pompiste

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_indexes');
    }
};
