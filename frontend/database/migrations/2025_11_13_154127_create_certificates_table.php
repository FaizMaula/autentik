<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tahun_akademik')->nullable();
            $table->string('penyelenggara');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai');
            $table->string('nama_kegiatan');
            $table->string('nama_kegiatan_inggris')->nullable();
            $table->string('berkas'); 
            $table->boolean('is_verified')->default(false);

            $table->longText('ocr_text')->nullable();
            $table->json('match_scores')->nullable();
            $table->longText('verifikasi_ai')->nullable();
            $table->float('final_score')->default(0);

            $table->timestamps();

        });
    }

    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
