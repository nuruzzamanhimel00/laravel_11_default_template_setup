<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\ProductCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupplierSeeder extends Seeder
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
                'first_name' => 'Supplier',
                'email'     => 'supplier@app.com',
                'password'  => Hash::make('12345678'),
                'phone'     => '+8801255456698',
                'type'      => User::TYPE_SUPPLIER,
                'suppliers' => [
                    'company'   => 'Supplier Company',
                    'designation'   => 'Supplier Company',
                    'address'   => '',
                    'country'   => '',
                    'city'   => '',
                    'zipcode'   => '',
                    'short_address'   => '',
                ]

            ]
        ];
        foreach($allData as $data) {
            $user = User::updateOrCreate([
                'email' => $data['email'],

            ], [
                'first_name' => $data['first_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
                'type' => $data['type'],
            ]);
            if($user){
                $user->supplier()->delete();
                $user->supplier()->create([
                    'company' => $data['suppliers']['company'],
                    'designation' => $data['suppliers']['designation'],
                    'address' => $data['suppliers']['address'],
                    'country' => $data['suppliers']['country'],
                    'city' => $data['suppliers']['city'],
                    'zipcode' => $data['suppliers']['zipcode'],
                    'short_address' => $data['suppliers']['short_address'],
                ]);
            }
        }


    }
}
