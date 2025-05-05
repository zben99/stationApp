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
        Schema::create('daily_simple_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('rotation', ['6-14', '14-22', '22-6']);
            $table->enum('type', ['boutique', 'lavage']);
            $table->decimal('amount', 10, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // ✅ Unicité : pas deux fois le même enregistrement pour la même rotation/type/date/station
            $table->unique(['station_id', 'date', 'rotation', 'type'], 'unique_station_date_rotation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_simple_revenues');
    }
};
