<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lms_question_banks', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id')->index(); $table->string('title',180); $table->unsignedBigInteger('created_by')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('lms_quizzes', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id')->index(); $table->unsignedBigInteger('course_lesson_id')->nullable()->index();
            $table->string('title',180); $table->text('description')->nullable(); $table->string('status',16)->default('draft')->index();
            $table->timestamp('opens_at')->nullable(); $table->timestamp('closes_at')->nullable(); $table->unsignedInteger('time_limit_seconds')->nullable();
            $table->unsignedSmallInteger('max_attempts')->default(1); $table->unsignedTinyInteger('pass_percent')->default(60);
            $table->boolean('shuffle_questions')->default(true); $table->boolean('manual_result_release')->default(false); $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        Schema::create('lms_questions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('course_id')->index(); $table->unsignedBigInteger('question_bank_id')->nullable()->index();
            $table->text('question_text'); $table->string('question_type',16); $table->text('explanation')->nullable(); $table->unsignedSmallInteger('points')->default(1);
            $table->string('status',16)->default('draft')->index(); $table->unsignedBigInteger('created_by')->nullable(); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('lms_question_options', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lms_question_id')->index(); $table->string('option_text',1000); $table->boolean('is_correct')->default(false); $table->unsignedSmallInteger('sort_order')->default(0); $table->timestamps();
        });
        Schema::create('lms_quiz_questions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lms_quiz_id')->index(); $table->unsignedBigInteger('lms_question_id')->index(); $table->unsignedSmallInteger('sort_order')->default(0); $table->timestamps();
            $table->unique(['lms_quiz_id','lms_question_id'],'lms_quiz_question_unique');
        });
        Schema::create('lms_quiz_attempts', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lms_quiz_id')->index(); $table->unsignedBigInteger('course_enrollment_id')->index();
            $table->unsignedInteger('attempt_number'); $table->string('status',20)->default('in_progress')->index(); $table->json('question_order');
            $table->timestamp('started_at'); $table->timestamp('deadline_at')->nullable(); $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('score')->nullable(); $table->unsignedInteger('total_points')->nullable(); $table->decimal('percentage',5,2)->nullable(); $table->boolean('passed')->nullable();
            $table->timestamp('result_released_at')->nullable(); $table->timestamps();
            $table->unique(['lms_quiz_id','course_enrollment_id','attempt_number'],'lms_quiz_attempt_number_unique');
        });
        Schema::create('lms_answers', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('lms_quiz_attempt_id')->index(); $table->unsignedBigInteger('lms_question_id')->index();
            $table->json('selected_option_ids'); $table->unsignedInteger('awarded_points')->default(0); $table->timestamps();
            $table->unique(['lms_quiz_attempt_id','lms_question_id'],'lms_answer_attempt_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_answers'); Schema::dropIfExists('lms_quiz_attempts'); Schema::dropIfExists('lms_quiz_questions');
        Schema::dropIfExists('lms_question_options'); Schema::dropIfExists('lms_questions'); Schema::dropIfExists('lms_quizzes'); Schema::dropIfExists('lms_question_banks');
    }
};
