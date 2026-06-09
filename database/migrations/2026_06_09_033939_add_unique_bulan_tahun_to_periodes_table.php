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
        Schema::table('periodes', function (Blueprint $table) {
            $table->unique(['bulan', 'tahun'], 'periodes_bulan_tahun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('periodes', function (Blueprint $table) {
            $table->dropUnique('periodes_bulan_tahun_unique');
        });
    }
};
