<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('nama', 100);
            $table->string('nik', 16);

            $table->string('no_rumah', 10);
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('no_hp', 15);

            $table->enum('jabatan', [
                'warga',
                'ketua_rt',
                'sekretaris_rt',
                'bendahara_rt',
                'ketua_rw',
                'sekretaris_rw',
                'bendahara_rw',
            ])->default('warga');

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

            $table->timestamps();

            $table->unique(['tenant_id', 'nik'], 'wargas_tenant_nik_unique');
            $table->index(['tenant_id', 'rt', 'rw']);
            $table->index(['tenant_id', 'status']);
            $table->index('jabatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};
