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
        Schema::create('tb_akurasi', function (Blueprint $table) {
            $table->id();
            $table->decimal('mape', 8, 4); // Mean Absolute Percentage Error
            $table->decimal('rmse', 12, 4); // Root Mean Square Error
            $table->decimal('mae', 12, 4); // Mean Absolute Error
            $table->decimal('r_squared', 8, 6); // R²
            $table->integer('data_points')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_akurasi');
    }
};
