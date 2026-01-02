<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_verifications', function (Blueprint $table) {
            $table->id();

            // Data input
            $table->string('nama');
            $table->string('tahun_akademik')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('nama_kegiatan');
            $table->string('nama_kegiatan_inggris')->nullable();

            // File sertifikat yang diupload
            $table->string('berkas'); // path file

            // Output OCR
            $table->longText('ocr_text')->nullable();

            // Match scores berupa JSON
            $table->json('match_scores')->nullable();

            // Verifikasi AI dari Gemini
            $table->longText('verifikasi_ai')->nullable();

            // Final score numeric
            $table->float('final_score')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_verifications');
    }
};
