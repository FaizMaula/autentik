<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Makes 'berkas' column nullable to support Internal certificates
     * which don't require file upload (verification via database only).
     * External certificates still require file upload.
     */
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('berkas')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('berkas')->nullable(false)->change();
        });
    }
};
