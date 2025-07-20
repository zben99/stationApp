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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number');
            $table->date('date');
            $table->enum('rotation', ['6-14', '14-22', '22-6']);
            $table->string('supplier_name');
            $table->decimal('amount_ht', 10, 2);
            $table->decimal('amount_ttc', 10, 2);
             // Ajout des colonnes amount_paid et amount_remaining
            $table->decimal('amount_paid', 10, 2)->default(0); // Montant payé
            $table->decimal('amount_remaining', 10, 2); // Montant restant dû
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
