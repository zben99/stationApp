<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('packagings', function (Blueprint $table) {
            // Renommer volume_litre -> quantity (optionnel)
            $table->renameColumn('volume_litre', 'quantity');

            // Ajouter le champ d’unité
            $table->string('unit')->default('L')->after('quantity'); // Exemple : L, kg, unité
        });
    }

    public function down(): void
    {
        Schema::table('packagings', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->renameColumn('quantity', 'volume_litre');
        });
    }
};

