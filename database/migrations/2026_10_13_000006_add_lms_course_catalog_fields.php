<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'publication_state')) $table->string('publication_state', 16)->default('draft')->index();
            if (!Schema::hasColumn('courses', 'difficulty')) $table->string('difficulty', 20)->nullable();
            if (!Schema::hasColumn('courses', 'language')) $table->string('language', 64)->nullable();
            if (!Schema::hasColumn('courses', 'prerequisites')) $table->json('prerequisites')->nullable();
            if (!Schema::hasColumn('courses', 'estimated_minutes')) $table->unsignedInteger('estimated_minutes')->nullable();
            if (!Schema::hasColumn('courses', 'capacity')) $table->unsignedInteger('capacity')->nullable();
        });
        DB::table('courses')->where('status', STATUS_ACTIVE)->update(['publication_state' => 'published']);
        DB::table('courses')->where('status', '!=', STATUS_ACTIVE)->update(['publication_state' => 'draft']);
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            foreach (['publication_state','difficulty','language','prerequisites','estimated_minutes','capacity'] as $column) if (Schema::hasColumn('courses', $column)) $table->dropColumn($column);
        });
    }
};
