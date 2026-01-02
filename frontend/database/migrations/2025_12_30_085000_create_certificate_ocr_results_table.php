<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificate_ocr_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('certificate_id')
                  ->constrained('certificates')
                  ->onDelete('cascade');

            $table->string('ocr_engine'); 
            // contoh: easyocr, trocr

            $table->longText('ocr_text')->nullable(); 
            // full OCR text

            $table->longText('ocr_details')->nullable(); 
            // json: bbox, confidence per word

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_ocr_results');
    }
};
