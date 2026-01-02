<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificate_analysis_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('certificate_id')
                  ->constrained('certificates')
                  ->onDelete('cascade');

            $table->longText('google_results')->nullable();
            $table->longText('font_results')->nullable();
            $table->longText('match_scores')->nullable();
            $table->longText('verifikasi_ai')->nullable();

            $table->string('analysis_version')->nullable();
            // contoh: v1.0, prompt_v2, model_gpt4

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_analysis_results');
    }
};
