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
        Schema::create('tb_harga_emas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->decimal('harga_usd', 12, 2); // Harga dalam USD/oz
            $table->decimal('harga_idr', 15, 2); // Harga dalam IDR/gram
            $table->decimal('perubahan_persen', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_harga_emas');
    }
};
