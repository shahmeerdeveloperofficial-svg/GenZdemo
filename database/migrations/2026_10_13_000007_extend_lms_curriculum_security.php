<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['course_lessons' => 'lesson', 'course_lectures' => 'lecture', 'course_resources' => 'resource'] as $tableName => $kind) {
            if (!Schema::hasTable($tableName)) continue;
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $kind) {
                if (!Schema::hasColumn($tableName, 'sort_order')) $table->unsignedInteger('sort_order')->default(0);
                if (!Schema::hasColumn($tableName, 'publication_state')) $table->string('publication_state', 16)->default('published')->index();
                if ($kind === 'lesson' && !Schema::hasColumn($tableName, 'lesson_type')) $table->string('lesson_type', 16)->default('video');
                if ($kind === 'lecture' && !Schema::hasColumn($tableName, 'is_preview')) $table->boolean('is_preview')->default(false);
                if ($kind === 'resource' && !Schema::hasColumn($tableName, 'is_preview')) $table->boolean('is_preview')->default(false);
                if ($kind === 'resource' && !Schema::hasColumn($tableName, 'completion_required')) $table->boolean('completion_required')->default(true);
            });
            // Preserve existing course material as published during the additive rollout.
            \Illuminate\Support\Facades\DB::table($tableName)->whereNull('publication_state')->update(['publication_state' => 'published']);
        }

        if (!Schema::hasTable('course_content_revisions')) {
            Schema::create('course_content_revisions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->index();
                $table->string('content_type', 16);
                $table->unsignedBigInteger('content_id');
                $table->unsignedInteger('revision_number');
                $table->json('snapshot');
                $table->unsignedBigInteger('edited_by')->nullable();
                $table->timestamps();
                $table->unique(['content_type', 'content_id', 'revision_number'], 'course_content_revision_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_content_revisions');
        foreach (['course_lessons' => ['sort_order','publication_state','lesson_type'], 'course_lectures' => ['sort_order','publication_state','is_preview'], 'course_resources' => ['sort_order','publication_state','is_preview','completion_required']] as $tableName => $columns) {
            if (!Schema::hasTable($tableName)) continue;
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                foreach ($columns as $column) if (Schema::hasColumn($tableName, $column)) $table->dropColumn($column);
            });
        }
    }
};
