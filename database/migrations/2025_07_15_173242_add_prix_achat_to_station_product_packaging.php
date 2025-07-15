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
        Schema::table('station_product_packaging', function (Blueprint $table) {
                $table->decimal('prix_achat', 10, 2)->nullable();  // Prix d'achat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('station_product_packaging', function (Blueprint $table) {
             $table->dropColumn('prix_achat');
        });
    }
};
