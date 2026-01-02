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
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('nim'); // Nomor Induk Mahasiswa
            $table->string('participant_name');
            $table->string('email')->nullable();
            $table->string('faculty')->nullable();
            $table->string('study_program')->nullable();
            $table->string('attendance_status')->default('present'); // present, absent, etc.
            $table->timestamps();
            
            // Index for faster lookup
            $table->index(['nim', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
