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
        Schema::create('password_changes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('status');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('oldPass');
            $table->string('newPass');
            $table->string('verifyID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_changes');
    }
};
