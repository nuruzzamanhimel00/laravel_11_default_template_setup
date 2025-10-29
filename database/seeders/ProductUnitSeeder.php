<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProductUnit;

class ProductUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Ps',
                'symbol' => 'ps',
                'type' => 'Quantity'
            ],
            [
                'name' => 'Kg',
                'symbol' => 'kg',
                'type' => 'weight'
            ],
            [
                'name' => 'ML',
                'symbol' => 'ml',
                'type' => 'volume'
            ],
            [
                'name' => 'Gram',
                'symbol' => 'gm',
                'type' => 'weight'
            ],
            [
                'name' => 'Liter',
                'symbol' => 'l',
                'type' => 'volume'
            ],
            [
                'name' => 'Pound',
                'symbol' => 'lb',
                'type' => 'weight'
            ],
            [
                'name' => 'inch',
                'symbol' => 'inch',
                'type' => 'length'
            ],
            [
                'name' => "Meter",
                'symbol' => 'm',
                'type' => 'length'
            ]
        ];

        foreach ($units as $unit) {
            ProductUnit::updateOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
