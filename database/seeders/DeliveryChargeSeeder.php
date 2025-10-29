<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Str;
use App\Models\DeliveryCharge;
use Illuminate\Database\Seeder;
use App\Models\ProductCondition;
use Illuminate\Support\Facades\DB;

class DeliveryChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allData = [
            [
                'title' => 'Inside Faridpur',
                'cost' => 50,
                'unique_key' => Str::uuid()->toString()
            ],
            // [
            //     'title' => 'Outside Dhaka',
            //     'cost' => 120,
            //     'unique_key' => Str::uuid()->toString()
            // ],

        ];
        foreach($allData as $data) {
            DeliveryCharge::updateOrCreate([
                'unique_key' => $data['unique_key'],

            ], $data);
        }


    }
}
