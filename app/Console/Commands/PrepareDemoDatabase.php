<?php

namespace App\Console\Commands;

use Database\Seeders\GenZDemoSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrepareDemoDatabase extends Command
{
    protected $signature = 'genz:prepare-demo {--force : Confirm this is the dedicated fresh demo database}';

    protected $description = 'Apply post-installer migrations, add missing permissions, and seed fictional GenZ demo workflows.';

    public function handle(): int
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('Production requires --force because this command adds synthetic demo records.');
            return self::FAILURE;
        }

        if (DB::connection()->getDatabaseName() !== config('genz.demo_database')) {
            $this->error('Refusing to seed a database other than the configured dedicated demo database.');
            return self::FAILURE;
        }

        if (!is_file(storage_path('installed'))) {
            $this->error('Run the web installer first. The storage/installed marker is missing.');
            return self::FAILURE;
        }

        if (!Schema::hasTable('users')) {
            $this->error('The installed database is missing its users table. No demo data was written.');
            return self::FAILURE;
        }

        $this->info('Applying migrations that the installer SQL import may have overwritten...');
        if ($this->call('migrate', ['--force' => true, '--no-interaction' => true]) !== self::SUCCESS) {
            $this->error('Migrations did not finish. Demo seeding was stopped.');
            return self::FAILURE;
        }

        $this->info('Adding current module permissions...');
        if ($this->call('db:seed', ['--class' => PermissionSeeder::class, '--force' => true]) !== self::SUCCESS) {
            $this->error('Permission seeding failed. Demo seeding was stopped.');
            return self::FAILURE;
        }

        config(['genz.demo_seed_enabled' => true]);
        $this->info('Adding fictional CRM, admissions, consultation, and LMS demonstration records...');
        if ($this->call('db:seed', ['--class' => GenZDemoSeeder::class, '--force' => true]) !== self::SUCCESS) {
            $this->error('Demo seeding failed. Review the command output and application log.');
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Demo database is prepared. No external email or payment was sent.');
        $this->warn('Store the one-time demo credentials privately and change them before handoff.');

        return self::SUCCESS;
    }
}
