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
        Schema::create('detail-reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('reseps');
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus');
            $table->integer('jumlah_bahan_resep');
            $table->string('satuan_detail_resep');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_reseps');
    }
};
