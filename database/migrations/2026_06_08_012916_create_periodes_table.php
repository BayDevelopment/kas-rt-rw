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
         Schema::create('periodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('bulan', 20);
            $table->year('tahun');

            $table->decimal('target_kas', 10, 2)->default(0);

            $table->enum('status', ['aktif', 'tutup'])->default('aktif');

            $table->timestamps();

            $table->unique(['tenant_id', 'bulan', 'tahun'], 'periodes_tenant_bulan_tahun_unique');
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};
