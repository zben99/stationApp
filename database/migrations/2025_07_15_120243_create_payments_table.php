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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('purchase_invoices')->onDelete('cascade'); // Référence à la facture
            $table->decimal('amount', 10, 2); // Montant du paiement
            $table->date('payment_date'); // Date du paiement
             $table->enum('rotation', ['6-14', '14-22', '22-6'])->nullable(); // Ajout de la rotation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
