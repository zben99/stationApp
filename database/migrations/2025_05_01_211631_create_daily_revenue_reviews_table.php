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
        Schema::create('daily_revenue_reviews', function (Blueprint $table) {
            $table->foreignId('station_id')->constrained();
            $table->date('date');
            $table->enum('rotation', ['6-14', '14-22', '22-6']);
            $table->decimal('fuel_amount', 10, 2)->default(0);
            $table->decimal('product_amount', 10, 2)->default(0);
            $table->decimal('shop_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_revenue_reviews');
    }
};
