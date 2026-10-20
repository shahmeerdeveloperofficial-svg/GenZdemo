<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lms_assignments', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id')->index(); $table->unsignedBigInteger('course_lesson_id')->nullable()->index();
            $table->string('title',180); $table->text('instructions')->nullable(); $table->timestamp('due_at')->nullable()->index();
            $table->unsignedSmallInteger('max_points')->default(100); $table->unsignedSmallInteger('max_submissions')->default(1);
            $table->string('late_policy',12)->default('accept'); $table->unsignedTinyInteger('late_penalty_percent')->default(0);
            $table->string('status',16)->default('draft')->index(); $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        Schema::create('lms_assignment_submissions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lms_assignment_id')->index(); $table->unsignedBigInteger('course_enrollment_id')->index();
            $table->unsignedBigInteger('user_id')->index(); $table->unsignedSmallInteger('version'); $table->string('status',16)->default('draft')->index();
            $table->text('text_response')->nullable(); $table->unsignedBigInteger('file_manager_id')->nullable(); $table->boolean('is_late')->default(false);
            $table->timestamp('submitted_at')->nullable(); $table->timestamps();
            $table->unique(['lms_assignment_id','course_enrollment_id','version'],'lms_assignment_submission_version_unique');
        });
        Schema::create('lms_grades', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lms_assignment_submission_id')->unique(); $table->unsignedBigInteger('grader_id')->index();
            $table->decimal('raw_score',7,2); $table->decimal('score',7,2); $table->unsignedSmallInteger('max_points'); $table->json('rubric')->nullable();
            $table->text('feedback')->nullable(); $table->timestamp('released_at')->nullable(); $table->timestamps();
        });
        Schema::create('lms_grade_history', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lms_grade_id')->index(); $table->unsignedBigInteger('actor_id')->index();
            $table->json('before_values')->nullable(); $table->json('after_values'); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_grade_history'); Schema::dropIfExists('lms_grades'); Schema::dropIfExists('lms_assignment_submissions'); Schema::dropIfExists('lms_assignments');
    }
};
