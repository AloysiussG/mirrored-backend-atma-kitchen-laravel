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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_transaksi_id')->constrained('status_transaksis');
            $table->foreignId('cart_id')->constrained('carts');
            $table->foreignId('alamat_id')->nullable()->constrained('alamats');
            $table->date('tanggal_pesan');
            $table->date('tanggal_lunas');
            $table->date('tanggal_ambil');
            $table->integer('poin_dipakai');
            $table->integer('poin_didapat');
            $table->integer('poin_sekarang');
            $table->float('tip');
            $table->float('ongkos_kirim');
            $table->float('potongan_harga');
            $table->float('total_harga');
            $table->string('no_nota');
            $table->string('kode_bukti_bayar')->nullable();
            $table->string('jenis_pengiriman');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
