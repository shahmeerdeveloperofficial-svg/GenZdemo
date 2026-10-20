<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->index();
            $table->string('event_key',100); $table->string('channel',20)->default('in_app');
            $table->boolean('enabled')->default(true); $table->timestamps();
            $table->unique(['user_id','event_key','channel'],'notification_preference_unique');
        });
        Schema::create('notification_outbox', function (Blueprint $table) {
            $table->id(); $table->string('dedupe_key',191)->unique(); $table->unsignedBigInteger('recipient_id')->index();
            $table->string('event_key',100)->index(); $table->string('channel',20)->default('in_app');
            $table->string('title',180); $table->text('body'); $table->text('link')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0); $table->timestamp('available_at')->index();
            $table->timestamp('sent_at')->nullable(); $table->text('last_error')->nullable(); $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('notification_outbox'); Schema::dropIfExists('notification_preferences'); }
};
