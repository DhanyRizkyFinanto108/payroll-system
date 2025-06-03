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
        Schema::create('gaji_bulanans', function (Blueprint $table) {
            $table->uuid('id_gaji')->primary();
            $table->uuid('id_absensi');
            $table->integer('nominal');
            $table->date('tanggal');
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('id_absensi')->references('id_absensi')->on('absensis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_bulanans');
    }
};
