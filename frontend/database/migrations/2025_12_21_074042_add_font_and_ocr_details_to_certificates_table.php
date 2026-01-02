<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->json('font_results')->nullable()->after('google_results');
            $table->json('ocr_details')->nullable()->after('font_results');
        });
    }

    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['font_results', 'ocr_details']);
        });
    }
};
