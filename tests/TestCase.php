<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.debug' => true]);

        // The build-version middleware expects a version row just like an installed
        // local app. Keep the fixture inside the in-memory test database only.
        if ($this->app->environment('testing') && Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['option_key' => 'build_version'],
                [
                    'option_value' => (string) config('app.build_version'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if ($this->app->environment('testing') && Schema::hasTable('languages')) {
            DB::table('languages')->updateOrInsert(
                ['id' => 1],
                [
                    'language' => 'English',
                    'iso_code' => 'en',
                    'rtl' => 0,
                    'status' => 1,
                    'default' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
