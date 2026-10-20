<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['option_key' => 'build_version', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'current_version', 'option_value' => '1.0',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'affiliate_commission_settings', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'default_affiliate_commission', 'option_value' => '10',  'created_at' => now(), 'updated_at' => now()],
            // Course settings
            ['option_key' => 'affiliate_commission_on_course', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'affiliate_commission_course_rate', 'option_value' => '10',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'affiliate_commission_course_levels', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
            // Service settings
            ['option_key' => 'affiliate_commission_on_service', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'affiliate_commission_service_rate', 'option_value' => '8',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'affiliate_commission_service_levels', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
            // Consultation settings
            ['option_key' => 'affiliate_commission_on_consultation', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'affiliate_commission_consultation_rate', 'option_value' => '12',  'created_at' => now(), 'updated_at' => now()],
            ['option_key' => 'affiliate_commission_consultation_levels', 'option_value' => '1',  'created_at' => now(), 'updated_at' => now()],
        ];
        Setting::insert($data);
    }
}
