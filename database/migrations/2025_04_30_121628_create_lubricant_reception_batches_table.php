<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lubricant_reception_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('num_bc')->nullable();
            $table->string('num_bl')->nullable();


            $table->date('date_reception');
             $table->enum('rotation', ['6-14', '14-22', '22-6']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lubricant_reception_batches');
    }
};
