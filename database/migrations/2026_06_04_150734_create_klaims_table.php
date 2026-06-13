<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klaims', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_temuan_id')
                ->constrained('laporan_temuans')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('bukti_kepemilikan');

            // 👈 TAMBAHAN: Kolom foto bertipe string dan boleh kosong (nullable)
            $table->string('foto')->nullable(); 

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak'
            ])->default('menunggu');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaims');
    }
};