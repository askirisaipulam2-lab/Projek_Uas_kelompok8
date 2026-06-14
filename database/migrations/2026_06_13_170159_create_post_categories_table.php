<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan perintah untuk membuat tabel (php artisan migrate).
     */
    public function up(): void
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Laporan Kehilangan, Laporan Temuan, Edukasi
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    /**
     * Jalankan perintah untuk membatalkan/menghapus tabel jika terjadi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_categories');
    }
};