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
        Schema::table('fuel_reception_lines', function (Blueprint $table) {
            $table->decimal('ecart_reception', 10, 2)->nullable()->after('jauge_apres');
            $table->decimal('ecart_stock', 10, 2)->nullable()->after('ecart_reception');
        });
    }

    public function down(): void
    {
        Schema::table('fuel_reception_lines', function (Blueprint $table) {
            $table->dropColumn(['ecart_reception', 'ecart_stock']);
        });
    }

};
