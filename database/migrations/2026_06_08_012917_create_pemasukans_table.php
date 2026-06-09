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
       Schema::create('pemasukans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('warga_id')
                ->constrained('wargas')
                ->cascadeOnDelete();

            $table->foreignId('periode_id')
                ->constrained('periodes')
                ->cascadeOnDelete();

            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal');

            $table->string('keterangan')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('warga_id');
            $table->index('periode_id');
            $table->index(['tenant_id', 'tanggal']);
            $table->index(['tenant_id', 'periode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukans');
    }
};
