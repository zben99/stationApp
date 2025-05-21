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
          Schema::table('station_products', function (Blueprint $table) {
            $table->string('code', 30)
                  ->after('category_id');      // position facultative
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('station_products', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
