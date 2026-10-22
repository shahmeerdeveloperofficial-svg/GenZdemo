<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_abroad_registrations', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->change();
            $table->date('signed_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Preserve existing rows; new unsigned registrations cannot be backfilled safely.
    }
};
