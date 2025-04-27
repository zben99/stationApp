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
        Schema::table('lubricant_receptions', function (Blueprint $table) {
            $table->foreignId('product_packaging_id')
                  ->after('station_product_id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('lubricant_receptions', function (Blueprint $table) {
            $table->dropForeign(['product_packaging_id']);
            $table->dropColumn('product_packaging_id');
        });
    }
};
