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
        // USERS AKU COMMENT
        
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('nama');
        //     $table->string('password');
        //     $table->string('email')->unique();
        //     $table->string('no_telp', 16)->unique();

        //     // sebenernya di bawah ini auto generate jadi belum kuhapus
        //     // tapi emang di database design (PDM) Atma Kitchen ga terlalu dibutuhin
        //     // khusus yang 'timestamps' kayaknya tetep penting soalnya bisa buat ->latest() atau ->oldest()

        //     // $table->timestamp('email_verified_at')->nullable();
        //     // $table->rememberToken();
        //     $table->timestamps();
        // });

        // ini aku comment dulu soalnya ngegenerate tabel yang belum dibutuhin

        // Schema::create('password_reset_tokens', function (Blueprint $table) {
        //     $table->string('email')->primary();
        //     $table->string('token');
        //     $table->timestamp('created_at')->nullable();
        // });

        // ini kayaknya tetep perlu soalnya Laravel 11 butuh table sessions untuk dirun (php artisan serve)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
