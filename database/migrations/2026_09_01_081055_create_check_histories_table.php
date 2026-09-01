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
        Schema::create('check_histories', function (Blueprint $table) {
            $table->id();
            $table->string('nolang', 20)->index();
            $table->string('status', 50);
            $table->string('nama_pelanggan')->nullable();
            $table->decimal('total_tagihan', 15, 2)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_histories');
    }
};
