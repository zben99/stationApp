<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
        {
            Schema::create('fuel_reception_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fuel_reception_id')->constrained()->onDelete('cascade');
                $table->foreignId('tank_id')->constrained()->onDelete('cascade');
                $table->string('produit');

                $table->decimal('jauge_avant', 10, 2)->nullable();
                $table->decimal('reception_par_cuve', 10, 2)->nullable();
                $table->decimal('jauge_apres', 10, 2)->nullable();
                $table->decimal('ecart', 10, 2)->nullable();

                $table->decimal('sonabhy_d', 8, 4)->nullable();
                $table->decimal('sonabhy_t', 5, 2)->nullable();
                $table->decimal('sonabhy_d15', 8, 4)->nullable();

                $table->decimal('station_d', 8, 4)->nullable();
                $table->decimal('station_t', 5, 2)->nullable();
                $table->decimal('station_d15', 8, 4)->nullable();

                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_reception_lines');
    }
};
