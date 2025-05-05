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
        Schema::table('lubricant_reception_batches', function (Blueprint $table) {
            $table->string('num_bc')->nullable()->after('supplier_id');
            $table->string('num_bl')->nullable()->after('num_bc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lubricant_reception_batches', function (Blueprint $table) {
            $table->dropColumn('num_bc');
            $table->dropColumn('num_bl');
        });
    }
};
