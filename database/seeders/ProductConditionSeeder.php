<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [
            ['name' => 'New', 'notes' => ''],
            ['name' => 'Used', 'notes' => ''],
        ];

        foreach ($conditions as $condition) {
            // create or update product condition
            \App\Models\ProductCondition::updateOrCreate(['name' => $condition['name']], $condition);
        }
    }
}
