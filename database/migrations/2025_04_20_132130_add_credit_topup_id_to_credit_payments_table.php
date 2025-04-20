<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credit_payments', function (Blueprint $table) {
            $table->foreignId('credit_topup_id')
                  ->after('client_id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('credit_payments', function (Blueprint $table) {
            $table->dropForeign(['credit_topup_id']);
            $table->dropColumn('credit_topup_id');
        });
    }
};

