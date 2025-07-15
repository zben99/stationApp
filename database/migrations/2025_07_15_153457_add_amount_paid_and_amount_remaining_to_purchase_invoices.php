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
        Schema::table('purchase_invoices', function (Blueprint $table) {
             // Ajout des colonnes amount_paid et amount_remaining
            $table->decimal('amount_paid', 10, 2)->default(0); // Montant payé
            $table->decimal('amount_remaining', 10, 2); // Montant restant dû
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
             // Suppression des colonnes en cas de rollback
            $table->dropColumn('amount_paid');
            $table->dropColumn('amount_remaining');
        });
    }
};
