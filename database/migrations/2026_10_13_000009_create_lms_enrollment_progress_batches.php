<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('course_enrollments','access_expires_at')) $table->timestamp('access_expires_at')->nullable()->index();
            if (!Schema::hasColumn('course_enrollments','expired_at')) $table->timestamp('expired_at')->nullable();
            if (!Schema::hasColumn('course_enrollments','revoked_at')) $table->timestamp('revoked_at')->nullable();
            if (!Schema::hasColumn('course_enrollments','revocation_reason')) $table->string('revocation_reason', 255)->nullable();
            if (!Schema::hasColumn('course_enrollments','enrollment_source')) $table->string('enrollment_source', 24)->default('legacy')->index();
            if (!Schema::hasColumn('course_enrollments','payment_id')) $table->unsignedBigInteger('payment_id')->nullable()->index();
        });
        if (!Schema::hasTable('course_batches')) {
            Schema::create('course_batches', function (Blueprint $table) {
                $table->id(); $table->unsignedBigInteger('course_id')->index(); $table->string('title',180);
                $table->timestamp('starts_at')->nullable(); $table->timestamp('ends_at')->nullable();
                $table->string('timezone',64)->default('UTC'); $table->unsignedInteger('capacity')->nullable();
                $table->string('status',16)->default('draft')->index(); $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps(); $table->softDeletes();
            });
        }
        if (!Schema::hasTable('course_batch_memberships')) {
            Schema::create('course_batch_memberships', function (Blueprint $table) {
                $table->id(); $table->unsignedBigInteger('course_batch_id')->index(); $table->unsignedBigInteger('course_enrollment_id')->unique();
                $table->timestamp('joined_at'); $table->timestamps();
            });
        }
        if (!Schema::hasTable('lms_lesson_progress')) {
            Schema::create('lms_lesson_progress', function (Blueprint $table) {
                $table->id(); $table->unsignedBigInteger('course_enrollment_id'); $table->unsignedBigInteger('course_resource_id');
                $table->unsignedBigInteger('user_id')->index(); $table->unsignedInteger('last_position_seconds')->default(0);
                $table->unsignedInteger('time_spent_seconds')->default(0); $table->timestamp('completed_at')->nullable();
                $table->timestamps(); $table->unique(['course_enrollment_id','course_resource_id'],'lms_progress_enrollment_resource_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_lesson_progress'); Schema::dropIfExists('course_batch_memberships'); Schema::dropIfExists('course_batches');
        Schema::table('course_enrollments', function (Blueprint $table) {
            foreach (['access_expires_at','expired_at','revoked_at','revocation_reason','enrollment_source','payment_id'] as $column) if (Schema::hasColumn('course_enrollments',$column)) $table->dropColumn($column);
        });
    }
};
