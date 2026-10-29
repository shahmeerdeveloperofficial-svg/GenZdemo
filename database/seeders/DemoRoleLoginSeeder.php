<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoRoleLoginSeeder extends Seeder
{
    /**
     * Create predictable local demo logins for the five core account types.
     * These credentials are for development/demo use only.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('Demo login seeding skipped in production.');
            return;
        }

        $accounts = [
            ['first_name' => 'Demo', 'last_name' => 'Admin', 'email' => 'admin@gmail.com', 'role' => USER_ROLE_ADMIN],
            ['first_name' => 'Demo', 'last_name' => 'Student', 'email' => 'student@gmail.com', 'role' => USER_ROLE_STUDENT],
            ['first_name' => 'Demo', 'last_name' => 'Staff', 'email' => 'staff@gmail.com', 'role' => USER_ROLE_STAFF],
            ['first_name' => 'Demo', 'last_name' => 'Consultant', 'email' => 'consultant@gmail.com', 'role' => USER_ROLE_CONSULTANT],
            ['first_name' => 'Demo', 'last_name' => 'Affiliate', 'email' => 'affiliate@gmail.com', 'role' => USER_ROLE_AFFILIATE],
        ];

        foreach ($accounts as $index => $account) {
            $user = User::firstOrNew(['email' => $account['email']]);
            if (!$user->exists) {
                $user->fill([
                    ...$account,
                    'mobile' => '0300000000' . ($index + 1),
                    'status' => USER_STATUS_ACTIVE,
                    'google_auth_status' => 0,
                    'google2fa_secret' => app('pragmarx.google2fa')->generateSecretKey(),
                    'affiliate_key' => $account['role'] === USER_ROLE_AFFILIATE
                        ? 'DEMOAFF' . Str::upper(Str::random(8))
                        : null,
                    'balance' => 0,
                ]);
            } elseif ((int) $user->role !== $account['role']) {
                throw new \RuntimeException("The demo email {$account['email']} is already used by a different account type.");
            }

            $user->password = Hash::make('123456');
            $user->save();
        }
    }
}
