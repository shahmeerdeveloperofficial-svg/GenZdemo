<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('user_id');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->string('transection_id', 80);
            $table->decimal('amount')->default(0.00);
            $table->string('payment_method', 100)->nullable();
            $table->mediumText('note', 100)->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=complete, 2=rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
