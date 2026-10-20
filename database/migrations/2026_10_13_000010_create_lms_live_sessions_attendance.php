<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('course_live_sessions')) Schema::create('course_live_sessions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id')->index(); $table->unsignedBigInteger('course_batch_id')->nullable()->index();
            $table->string('title',180); $table->text('description')->nullable(); $table->string('provider',24)->default('external');
            $table->text('meeting_url')->nullable(); $table->text('recording_url')->nullable(); $table->timestamp('starts_at')->index();
            $table->unsignedSmallInteger('duration_minutes')->default(60); $table->string('timezone',64)->default('UTC');
            $table->string('status',16)->default('scheduled')->index(); $table->unsignedInteger('schedule_revision')->default(1);
            $table->unsignedBigInteger('created_by')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        if (!Schema::hasTable('course_live_attendance')) Schema::create('course_live_attendance', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_live_session_id'); $table->unsignedBigInteger('course_enrollment_id');
            $table->unsignedBigInteger('user_id')->index(); $table->timestamp('joined_at')->nullable(); $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('left_at')->nullable(); $table->unsignedInteger('attended_seconds')->default(0);
            $table->string('attendance_status',16)->default('registered'); $table->timestamps();
            $table->unique(['course_live_session_id','course_enrollment_id'],'course_live_attendance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_live_attendance'); Schema::dropIfExists('course_live_sessions');
    }
};
