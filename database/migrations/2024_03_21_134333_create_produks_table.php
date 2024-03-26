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
        Schema::create('produks', function (Blueprint $table) {
            //disini aku gak buat fk yang detail hampers,soalnya itu harusnya di detail hampers.
            $table->id();
            $table->foreignId('kategori_produk_id')->constrained('kategori_produks');
            $table->foreignId('penitip_id')->nullable()->constrained('penitips');
            $table->string('nama_produk');
            $table->integer('jumlah_stock')->nullable();
            $table->string('status');
            $table->float('harga');
            // $table->float('harga_setengah')->nullable();
            $table->integer('kuota_harian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
