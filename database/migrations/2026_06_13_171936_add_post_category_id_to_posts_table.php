<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Menambahkan kolom foreign key yang mengarah ke tabel post_categories
            // nullable() digunakan agar data post lama yang sudah ada tidak memicu eror saat migrasi
            $table->foreignId('post_category_id')->nullable()->after('slug')->constrained('post_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Menghapus kembali foreign key dan kolomnya jika di-rollback
            $table->dropForeign(['post_category_id']);
            $table->dropColumn('post_category_id');
        });
    }
};