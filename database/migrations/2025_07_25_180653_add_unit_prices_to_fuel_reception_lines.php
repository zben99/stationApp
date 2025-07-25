<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('fuel_reception_lines', function (Blueprint $table) {
            $table->decimal('unit_price_purchase', 10, 2)->nullable()->after('reception_par_cuve');
            $table->decimal('unit_price_sale', 10, 2)->nullable()->after('unit_price_purchase');
        });
    }

    public function down(): void {
        Schema::table('fuel_reception_lines', function (Blueprint $table) {
            $table->dropColumn(['unit_price_purchase', 'unit_price_sale']);
        });
    }
};

