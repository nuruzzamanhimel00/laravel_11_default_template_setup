<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\ProductCondition;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allData = [
            'Walmart',
            'Kroger',
            'Costco',
            'Albertsons',
            'Trader Joe',
            'Whole Foods',
            'Publix'

        ];
        foreach($allData as $data) {
            Brand::updateOrCreate([
                'name' => $data,

            ], [
                'name' => $data,

            ]);
        }


    }
}
