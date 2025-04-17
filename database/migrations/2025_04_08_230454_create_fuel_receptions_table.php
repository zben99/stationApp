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
        Schema::create('fuel_receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->date('date_reception');
            $table->string('num_bl')->nullable();

            $table->foreignId('transporter_id')->constrained()->onDelete('set null')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');

            $table->decimal('contre_plein_litre', 10, 2)->nullable();
            $table->decimal('contre_plein_valeur', 12, 2)->nullable();

            $table->enum('observation_type', ['prêt', 'remboursement'])->nullable();
            $table->decimal('observation_litre', 10, 2)->nullable();

            $table->text('remarques')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_receptions');
    }
};
