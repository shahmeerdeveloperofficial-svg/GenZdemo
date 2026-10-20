<?php

namespace Database\Seeders;

use App\Models\Gateway;
use App\Models\GatewayCurrency;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];

        Gateway::insert($data);

        GatewayCurrency::insert([

        ]);
    }
}
