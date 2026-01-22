<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds tanggal_kegiatan column for single-day events
     * and makes tanggal_selesai nullable to support both single-day and multi-day events.
     */
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->date('tanggal_kegiatan')->nullable()->after('penyelenggara');
            $table->date('tanggal_selesai')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('tanggal_kegiatan');
            $table->date('tanggal_selesai')->nullable(false)->change();
        });
    }
};
