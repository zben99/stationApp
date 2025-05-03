<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyRevenueValidationsTable extends Migration
{
    public function up()
    {
        Schema::create('daily_revenue_validations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('rotation', ['6-14', '14-22', '22-6']);

            $table->decimal('fuel_amount', 12, 2)->default(0);            // Vente de carburant
            $table->decimal('product_amount', 12, 2)->default(0);         // Vente de lubrifiants, etc.
            $table->decimal('shop_amount', 12, 2)->default(0);            // Boutique (si gérée séparément)

            $table->decimal('credit_received', 12, 2)->default(0);        // Crédits reçus
            $table->decimal('credit_refunded', 12, 2)->default(0);        // Remboursements de crédit

            $table->decimal('balance_received', 12, 2)->default(0);       // Avoirs perçus
            $table->decimal('balance_used', 12, 2)->default(0);           // Avoirs servis

            $table->decimal('tpe_amount', 12, 2)->default(0);             // Paiements TPE
            $table->decimal('om_amount', 12, 2)->default(0);              // Paiements Orange Money
            $table->decimal('cash_amount', 12, 2)->default(0);            // Espèces

            $table->decimal('expenses', 12, 2)->default(0);               // Dépenses

            $table->decimal('net_to_deposit', 12, 2)->default(0);         // Montant à reverser

            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_revenue_validations');
    }
}
