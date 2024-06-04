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
        Schema::table('reseps', function (Blueprint $table) {
            // new:: produk_id fk to 'produk_uniques', bukan ke 'produks' lagi
            $table->dropForeign(['produk_id']);
            $table->foreignId('produk_id')->change()->constrained('produk_uniques');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
            $table->foreignId('produk_id')->change()->constrained('produks');
        });
    }
};
