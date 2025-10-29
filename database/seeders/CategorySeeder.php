<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\ProductCondition;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allData = [
            'Fruits & Vegetables',
            'Dairy & Eggs',
            'Meat & Seafood',
            'Bakery & Bread',
            'Pantry Staples ',
            'Beverages',
            'Frozen Foods',

        ];
        foreach($allData as $data) {
            Category::updateOrCreate([
                'name' => $data,
                'slug' => Str::slug($data)
            ], [
                'name' => $data,
                'slug' => Str::slug($data)
            ]);
        }


    }
}
