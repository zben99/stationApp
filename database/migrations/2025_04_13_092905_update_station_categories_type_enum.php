<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE station_categories MODIFY type ENUM('fuel', 'lubrifiant', 'boutique') DEFAULT 'fuel'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
