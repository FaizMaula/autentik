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
        Schema::table('certificates', function (Blueprint $table) {
            $table->enum('certificate_type', ['internal', 'external'])->default('external')->after('user_id');
            $table->string('nim')->nullable()->after('certificate_type');
            $table->boolean('internal_verified')->default(false)->after('nim');
            $table->text('internal_verification_notes')->nullable()->after('internal_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['certificate_type', 'nim', 'internal_verified', 'internal_verification_notes']);
        });
    }
};
