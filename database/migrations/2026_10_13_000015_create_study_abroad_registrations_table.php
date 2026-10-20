<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_abroad_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->string('full_name', 180);
            $table->string('father_name', 180);
            $table->text('identity_number'); // encrypted by the model accessor
            $table->date('date_of_birth');
            $table->string('gender', 24);
            $table->string('marital_status', 24);
            $table->string('phone', 60)->index();
            $table->string('whatsapp', 60);
            $table->string('email')->index();
            $table->text('residential_address');
            $table->string('last_qualification', 180);
            $table->string('field_of_study', 180);
            $table->string('institute_name', 180);
            $table->unsignedSmallInteger('passing_year');
            $table->string('grade', 60);
            $table->string('destination', 80);
            $table->string('branch', 80);
            $table->string('program_type', 32);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('paid_currency', 3);
            $table->string('signature_path', 255);
            $table->date('signed_at');
            $table->boolean('terms_accepted')->default(false);
            $table->string('status', 24)->default('submitted')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_abroad_registrations');
    }
};
