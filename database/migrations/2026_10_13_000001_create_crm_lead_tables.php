<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 120);
            $table->string('last_name', 120)->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone', 60)->nullable()->index();
            $table->string('status', 32)->default('new')->index();
            $table->string('source', 80)->default('manual')->index();
            $table->string('source_detail')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->unsignedBigInteger('converted_user_id')->nullable()->index();
            $table->string('lost_reason')->nullable();
            $table->json('tags')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamp('first_contacted_at')->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('consultation_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_lead_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('type', 40)->index();
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_lead_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->index();
            $table->unsignedBigInteger('author_id')->nullable()->index();
            $table->text('body');
            $table->boolean('is_private')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->index();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->timestamp('due_at')->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_follow_ups');
        Schema::dropIfExists('crm_lead_notes');
        Schema::dropIfExists('crm_lead_activities');
        Schema::dropIfExists('crm_leads');
    }
};
