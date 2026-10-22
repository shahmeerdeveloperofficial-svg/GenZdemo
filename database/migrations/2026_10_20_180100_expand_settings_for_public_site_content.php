<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('option_value')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keep the wider column on rollback so saved website content is not truncated.
    }
};
