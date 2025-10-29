<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         $user = User::updateOrCreate(
            [
                'email' => 'restaurant@app.com',
            ]
            ,[
            'first_name'            => 'Restaurant',
            'last_name'            => '',
            'username'              => 'restaurant',
            'email'                 => 'restaurant@app.com',
            'phone'                 => '+8801566584489',
            'email_verified_at'     => now(),
            'password'              => \Illuminate\Support\Facades\Hash::make('12345678'),
            'status'                => User::STATUS_ACTIVE,
            'type'                  => User::TYPE_RESTAURANT,
            'remember_token'        => Str::random(10),
        ]);

        $user->restaurant()->updateOrCreate([
           'manager_phone' => '+88015622343',
        ]);

    }
}
