<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            // 1. Putuskan hubungan paksa (foreign key) lama ke tabel 'categories'
            $table->dropForeign('posts_category_id_foreign');
            
            // 2. Buat hubungan paksa baru agar 'category_id' kini patuh ke tabel 'post_categories'
            $table->foreign('category_id')
                  ->references('id')
                  ->on('post_categories')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            // Mengembalikan ke aturan lama jika di-rollback
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }
};
