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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained('users');
            $table->foreignId('role_id')->constrained('roles');

            // ini sebenernya udah ada di user tapi entah kenapa di PDM tetep ada
            // mungkin karena relasi generalisasi spesialisasi
            // sementara aku include dulu
            $table->string('nama');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('no_telp', 16)->unique();

            $table->date('hire_date');
            $table->float('gaji');
            $table->float('bonus_gaji');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
