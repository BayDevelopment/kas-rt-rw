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
         Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('periode_id')
                ->constrained('periodes')
                ->cascadeOnDelete();

            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal');

            $table->enum('kategori', [
                'operasional',
                'sosial',
                'pembangunan',
                'lainnya',
            ]);

            $table->string('keterangan');

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('periode_id');
            $table->index(['tenant_id', 'tanggal']);
            $table->index(['tenant_id', 'periode_id']);
            $table->index(['tenant_id', 'kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
