<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $google2fa = app('pragmarx.google2fa');
        User::insert([
            ['first_name' => 'Administrator', 'last_name' => 'Doe', 'mobile' => '0', 'role' => USER_ROLE_ADMIN, 'email' => 'admin@gmail.com', 'password' => Hash::make(123456), 'status' => USER_STATUS_ACTIVE, 'google2fa_secret' => $google2fa->generateSecretKey(), 'affiliate_key' => null, 'balance' => 0],
            ['first_name' => 'Staff', 'last_name' => 'Doe', 'mobile' => '1', 'role' => USER_ROLE_STAFF, 'email' => 'staff@gmail.com', 'password' => Hash::make(123456), 'status' => USER_STATUS_ACTIVE, 'google2fa_secret' => $google2fa->generateSecretKey(), 'affiliate_key' => null, 'balance' => 0],
            ['first_name' => 'Student', 'last_name' => 'Doe', 'mobile' => '2', 'role' => USER_ROLE_STUDENT, 'email' => 'student@gmail.com', 'password' => Hash::make(123456), 'status' => USER_STATUS_ACTIVE, 'google2fa_secret' => $google2fa->generateSecretKey(), 'affiliate_key' => null, 'balance' => 0],
            ['first_name' => 'Consultant', 'last_name' => 'Doe', 'mobile' => '3', 'role' => USER_ROLE_CONSULTANT, 'email' => 'consultant@gmail.com', 'password' => Hash::make(123456), 'status' => USER_STATUS_ACTIVE, 'google2fa_secret' => $google2fa->generateSecretKey(), 'affiliate_key' => null, 'balance' => 0],
            ['first_name' => 'Ashik', 'last_name' => 'Rahman', 'email' => 'affiliate@gmail.com', 'mobile' => '4', 'password' => Hash::make(123456), 'role' => USER_ROLE_AFFILIATE, 'google2fa_secret' => $google2fa->generateSecretKey(), 'status' => USER_STATUS_ACTIVE, 'affiliate_key' => 'AFFHRHON9MC', 'balance' => 0],
        ]);
    }
}
