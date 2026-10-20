<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'consultation_timezone')) $table->string('consultation_timezone', 64)->nullable();
        });
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'consultation_timezone')) $table->string('consultation_timezone', 64)->nullable();
            if (!Schema::hasColumn('appointments', 'hold_expires_at')) $table->timestamp('hold_expires_at')->nullable()->index();
            if (!Schema::hasColumn('appointments', 'cancellation_reason')) $table->string('cancellation_reason', 1000)->nullable();
            if (!Schema::hasColumn('appointments', 'rescheduled_from_id')) $table->unsignedBigInteger('rescheduled_from_id')->nullable()->index();
        });
        Schema::create('consultation_booking_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('event_type', 24);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
        Schema::create('consultation_blackout_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultant_id')->index();
            $table->date('date');
            $table->string('reason', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->unique(['consultant_id','date'], 'consultation_blackout_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_blackout_dates');
        Schema::dropIfExists('consultation_booking_events');
        Schema::table('appointments', function (Blueprint $table) {
            foreach (['consultation_timezone','hold_expires_at','cancellation_reason','rescheduled_from_id'] as $column) {
                if (!Schema::hasColumn('appointments', $column)) continue;
                if (in_array($column, ['hold_expires_at', 'rescheduled_from_id'], true) && Schema::hasIndex('appointments', [$column])) $table->dropIndex([$column]);
                $table->dropColumn($column);
            }
        });
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'consultation_timezone')) $table->dropColumn('consultation_timezone');
        });
    }
};
