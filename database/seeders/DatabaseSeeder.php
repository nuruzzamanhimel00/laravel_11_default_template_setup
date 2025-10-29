<?php

namespace Database\Seeders;

use App\Models\Patient;
use Database\Factories\PatientFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductConditionSeeder::class,
            WarehouseSeeder::class,
            AttributeSeeder::class,
            ProductUnitSeeder::class,
            SupplierSeeder::class,
            // PromotionTableSeeder::class,
            RegularUserSeeder::class,
            RestaurantSeeder::class,
            DeliveryChargeSeeder::class
        ]);
    }
}
