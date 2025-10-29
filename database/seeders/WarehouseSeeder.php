<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Main Warehouse',
                'code' => 'MWH',
                'address' => '123 Main St, Anytown, USA',
                'phone' => '555-1234',
                'email' => 'd0BZt@example.com',
                'is_default' => true
            ]
        ];

        foreach ($warehouses as $warehouse) {
            \App\Models\Warehouse::updateOrCreate(['name' => $warehouse['name']], $warehouse);
        }
    }
}
