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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained('users');

            // ini sebenernya udah ada di user tapi entah kenapa di PDM tetep ada
            // mungkin karena relasi generalisasi spesialisasi
            // sementara aku include dulu
            $table->string('nama');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('no_telp', 16)->unique();

            $table->float('saldo');
            $table->integer('poin');
            $table->date('tanggal_lahir');
            $table->string('foto_profile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
