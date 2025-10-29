<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegularUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         User::updateOrCreate(
            [
                'email' => 'user@app.com',
            ]
            ,[
            'first_name'            => 'user',
            'last_name'            => '',
            'username'              => 'user',
            'email'                 => 'user@app.com',
            'phone'                 => '+8801223423',
            'email_verified_at'     => now(),
            'password'              => \Illuminate\Support\Facades\Hash::make('12345678'),
            'status'                => User::STATUS_ACTIVE,
            'type'                  => User::TYPE_REGULAR_USER,
            'remember_token'        => Str::random(10),
        ]);


    }
}
