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
        Schema::create('certificate_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('old_status', 50); // verified, suspicious, not_verified
            $table->string('new_status', 50); // verified, not_verified
            $table->float('old_score')->nullable();
            $table->float('new_score')->nullable();
            $table->text('notes')->nullable(); // Admin notes/reason for status change
            $table->timestamps();

            // Index for faster queries
            $table->index(['certificate_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_status_logs');
    }
};
