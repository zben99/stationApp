<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('packagings', function (Blueprint $table) {
            $table->string('type')->nullable()->after('label'); // Ex : lubrifiant, gaz, lavage
        });
    }

    public function down(): void
    {
        Schema::table('packagings', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

