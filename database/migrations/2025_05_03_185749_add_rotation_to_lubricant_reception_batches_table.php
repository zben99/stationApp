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
        Schema::table('lubricant_reception_batches', function (Blueprint $table) {
            $table->string('rotation')->after('date_reception')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lubricant_reception_batches', function (Blueprint $table) {
            $table->dropColumn('rotation');
        });
    }
};
