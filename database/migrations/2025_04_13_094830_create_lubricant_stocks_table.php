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
        Schema::create('lubricant_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_packaging_id')->constrained()->onDelete('cascade');
            $table->decimal('quantite_actuelle', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['station_product_id', 'product_packaging_id']); // pour éviter doublons
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lubricant_stocks');
    }
};
