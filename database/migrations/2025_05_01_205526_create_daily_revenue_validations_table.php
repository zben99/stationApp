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

            /* === Carburants === */
            $table->decimal('fuel_super_amount', 12, 2)->default(0);
            $table->decimal('fuel_gazoil_amount', 12, 2)->default(0);

            /* === Produits par famille === */
            $table->decimal('lub_amount', 12, 2)->default(0);
            $table->decimal('pea_amount', 12, 2)->default(0);
            $table->decimal('gaz_amount', 12, 2)->default(0);
            $table->decimal('lampes_amount', 12, 2)->default(0);
              $table->decimal('divers_amount', 12, 2)->default(0);
            $table->decimal('lavage_amount', 12, 2)->default(0);
            $table->decimal('boutique_amount', 12, 2)->default(0);

            /* === Crédits & avoirs === */
            $table->decimal('credit_received', 12, 2)->default(0);
            $table->decimal('credit_repaid', 12, 2)->default(0);
            $table->decimal('balance_received', 12, 2)->default(0);
            $table->decimal('balance_used', 12, 2)->default(0);

            /* === Mouvements électroniques === */
            $table->decimal('tpe_amount', 12, 2)->default(0);
            $table->decimal('tpe_recharge_amount', 12, 2)->default(0)->comment('Montant des recharges TPE');
            $table->decimal('om_recharge_amount', 12, 2)->default(0)->comment('Montant des recharges OM');
            $table->decimal('om_amount', 12, 2)->default(0);

            /* === Décaissements : dépenses espèces === */
            $table->decimal('expenses', 12, 2)->default(0);

            /* === Cash déclaré & net calculé === */
            $table->decimal('cash_amount', 12, 2)->default(0);   // montant effectivement remis
            $table->decimal('net_to_deposit', 12, 2)->default(0);   // calcul appli

            $table->foreignId('validated_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            /* Unicité d’une validation par station+date+rotation */
            $table->unique(['station_id', 'date', 'rotation']);
        });

    }

    public function down()
    {
        Schema::dropIfExists('daily_revenue_validations');
    }
}
