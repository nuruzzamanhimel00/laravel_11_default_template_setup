<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\ProductCondition;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;

class PromotionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the promotions data
        $promotions = [
            [
                'id' => 1,
                'title' => 'Summer Sale',
                'message' => 'Get up to 50% off on all groceries!',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(10),
                'target_type' => User::TYPE_REGULAR_USER,
                'status' => STATUS_ACTIVE,
            ],
            [
                "id" => 2,
                'title' => 'Restaurant Discount',
                'message' => 'Special discount for restaurant partners!',
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(15),
                'target_type' => User::TYPE_RESTAURANT,
                'status' => STATUS_ACTIVE,
            ],
            [
                'id' => 3,
                'title' => 'Winter Special',
                'message' => 'Enjoy winter special deals on selected items!',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(20),
                'target_type' => User::TYPE_REGULAR_USER,
                'status' => STATUS_INACTIVE,
            ],
            [
                'id' => 4,
                'title' => 'New Year Offer',
                'message' => 'Celebrate New Year with exclusive offers!',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(30),
                'target_type' => User::TYPE_REGULAR_USER,
                'status' => STATUS_ACTIVE,
            ],
        ];

        // Insert the promotions into the database

        foreach($promotions as $data) {
            Promotion::updateOrCreate([
                'id' => $data['id'],

            ], $data);
        }


    }
}
