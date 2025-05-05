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
        Schema::create('daily_product_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_packaging_id')->constrained('station_product_packaging')->onDelete('cascade');
            $table->date('date');
            $table->enum('rotation', ['6-14', '14-22', '22-6']); // Rotation
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['station_id', 'date', 'rotation', 'product_packaging_id'], 'unique_daily_product_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_product_sales');
    }
};
