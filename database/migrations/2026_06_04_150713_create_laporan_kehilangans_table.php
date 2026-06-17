<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan_kehilangans', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('kategori_id')
                ->constrained('kategoris')
                ->cascadeOnDelete();

            $table->foreignId('lokasi_id')
                ->constrained('lokasis')
                ->cascadeOnDelete();

            $table->string('judul');

            $table->text('deskripsi');

            $table->date('tanggal_hilang');

            $table->string('gambar')->nullable();

            $table->enum('status', [
                'hilang',
                'ditemukan',
                'diklaim'
            ])->default('hilang');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kehilangans');
    }
};
