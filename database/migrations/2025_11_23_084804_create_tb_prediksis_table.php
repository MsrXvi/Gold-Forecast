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
        Schema::create('tb_prediksi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_prediksi');
            $table->decimal('harga_prediksi_usd', 12, 2);
            $table->decimal('harga_prediksi_idr', 15, 2);
            $table->decimal('lower_bound', 12, 2)->nullable();
            $table->decimal('upper_bound', 12, 2)->nullable();
            $table->integer('periode_tahun')->default(5);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_prediksi');
    }
};
