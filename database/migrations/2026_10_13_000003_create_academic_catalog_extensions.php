<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasColumn('countries', 'country_code')) $table->string('country_code', 3)->nullable()->index();
            if (!Schema::hasColumn('countries', 'currency_code')) $table->string('currency_code', 3)->nullable();
            if (!Schema::hasColumn('countries', 'source_url')) $table->string('source_url', 1000)->nullable();
            if (!Schema::hasColumn('countries', 'last_verified_at')) $table->timestamp('last_verified_at')->nullable();
        });

        Schema::table('universities', function (Blueprint $table) {
            if (!Schema::hasColumn('universities', 'city')) $table->string('city', 160)->nullable()->index();
            if (!Schema::hasColumn('universities', 'website_url')) $table->string('website_url', 1000)->nullable();
            if (!Schema::hasColumn('universities', 'tuition_currency')) $table->string('tuition_currency', 3)->nullable();
            if (!Schema::hasColumn('universities', 'source_url')) $table->string('source_url', 1000)->nullable();
            if (!Schema::hasColumn('universities', 'last_verified_at')) $table->timestamp('last_verified_at')->nullable();
        });

        Schema::table('scholarships', function (Blueprint $table) {
            if (!Schema::hasColumn('scholarships', 'eligibility_conditions')) $table->text('eligibility_conditions')->nullable();
            if (!Schema::hasColumn('scholarships', 'source_url')) $table->string('source_url', 1000)->nullable();
            if (!Schema::hasColumn('scholarships', 'last_verified_at')) $table->timestamp('last_verified_at')->nullable();
        });

        Schema::create('academic_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id')->index();
            $table->unsignedBigInteger('country_id')->index();
            $table->unsignedBigInteger('study_level_id')->nullable()->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->unsignedBigInteger('legacy_subject_id')->nullable()->unique();
            $table->string('title', 255);
            $table->string('slug', 191)->unique();
            $table->string('degree_award', 160)->nullable();
            $table->text('description')->nullable();
            $table->string('duration', 120)->nullable();
            $table->decimal('tuition_amount', 12, 2)->nullable();
            $table->string('tuition_currency', 3)->nullable();
            $table->json('entry_requirements')->nullable();
            $table->string('source_url', 1000)->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->string('status', 24)->default('draft')->index();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('program_intakes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_program_id')->index();
            $table->string('label', 120);
            $table->date('start_date')->nullable();
            $table->date('application_deadline')->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->string('source_url', 1000)->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_saved_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('item_type', 32);
            $table->unsignedBigInteger('item_id');
            $table->timestamps();
            $table->unique(['user_id', 'item_type', 'item_id'], 'student_saved_options_unique');
            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_saved_options');
        Schema::dropIfExists('program_intakes');
        Schema::dropIfExists('academic_programs');
        Schema::table('scholarships', function (Blueprint $table) {
            foreach (['eligibility_conditions', 'source_url', 'last_verified_at'] as $column) if (Schema::hasColumn('scholarships', $column)) $table->dropColumn($column);
        });
        Schema::table('universities', function (Blueprint $table) {
            foreach (['city', 'website_url', 'tuition_currency', 'source_url', 'last_verified_at'] as $column) if (Schema::hasColumn('universities', $column)) $table->dropColumn($column);
        });
        Schema::table('countries', function (Blueprint $table) {
            foreach (['country_code', 'currency_code', 'source_url', 'last_verified_at'] as $column) if (Schema::hasColumn('countries', $column)) $table->dropColumn($column);
        });
    }
};
