<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('crm_follow_ups', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_follow_ups','priority')) $table->string('priority', 12)->default('normal')->index();
            if (!Schema::hasColumn('crm_follow_ups','status')) $table->string('status', 16)->default('open')->index();
        });
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications','dedupe_key')) $table->string('dedupe_key', 191)->nullable()->unique();
        });
        if (!Schema::hasTable('crm_follow_up_comments')) {
            Schema::create('crm_follow_up_comments', function (Blueprint $table) {
                $table->id(); $table->unsignedBigInteger('follow_up_id')->index(); $table->unsignedBigInteger('author_id')->index();
                $table->text('body'); $table->timestamps();
            });
        }
        if (!Schema::hasTable('crm_task_notification_outbox')) {
            Schema::create('crm_task_notification_outbox', function (Blueprint $table) {
                $table->id(); $table->string('dedupe_key', 191)->unique(); $table->unsignedBigInteger('recipient_id')->index();
                $table->string('title', 180); $table->text('body'); $table->text('link')->nullable();
                $table->unsignedSmallInteger('attempts')->default(0); $table->timestamp('available_at')->index();
                $table->timestamp('sent_at')->nullable(); $table->text('last_error')->nullable(); $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_task_notification_outbox');
        Schema::dropIfExists('crm_follow_up_comments');
        if (Schema::hasColumn('notifications','dedupe_key')) Schema::table('notifications', function (Blueprint $table) { $table->dropUnique(['dedupe_key']); $table->dropColumn('dedupe_key'); });
        Schema::table('crm_follow_ups', function (Blueprint $table) {
            foreach (['priority','status'] as $column) if (Schema::hasColumn('crm_follow_ups',$column)) $table->dropColumn($column);
        });
    }
};
