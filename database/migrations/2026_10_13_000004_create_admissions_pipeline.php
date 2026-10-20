<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code', 32)->unique();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('university_id')->index();
            $table->unsignedBigInteger('academic_program_id')->index();
            $table->unsignedBigInteger('program_intake_id')->nullable()->index();
            $table->unsignedBigInteger('counselor_id')->nullable()->index();
            $table->date('application_deadline')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->string('visa_status', 24)->default('not_started')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['student_id', 'academic_program_id', 'status'], 'admissions_student_program_status_idx');
        });

        Schema::create('application_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admission_application_id')->index();
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32);
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admission_application_id')->index();
            $table->unsignedBigInteger('student_document_id')->index();
            $table->unsignedBigInteger('linked_by')->index();
            $table->timestamps();
            $table->unique(['admission_application_id', 'student_document_id'], 'application_document_unique');
        });

        Schema::create('application_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admission_application_id')->index();
            $table->unsignedBigInteger('author_id')->index();
            $table->text('body');
            $table->string('visibility', 16)->default('internal')->index();
            $table->timestamps();
        });

        Schema::create('application_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admission_application_id')->index();
            $table->unsignedBigInteger('assignee_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->timestamp('due_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_tasks');
        Schema::dropIfExists('application_notes');
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('application_status_history');
        Schema::dropIfExists('admission_applications');
    }
};
