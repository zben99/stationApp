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
                // Produits
                Schema::create('products', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->foreignId('category_id')->constrained()->onDelete('cascade');
                    $table->decimal('price', 10, 2);
                    $table->timestamps();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
