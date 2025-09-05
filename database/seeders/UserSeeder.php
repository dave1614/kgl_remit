<?php

namespace Database\Seeders;

use App\Functions\UsefulFunctions;
use App\Models\InecWard;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $ward = InecWard::find(5872);
        User::create([
            'id' => 10,
            'user_name' => 'admin',
            'slug' =>'admin',
            'name' => 'Admin One',
            'is_admin' => 1,
            'phone' => '08174622455',
            'email' => 'kgl_remit@gmail.com',
            'email_verified_at' => date("Y-m-d H:i:s"),
            'country_id' => 151,
            'password' => Hash::make('Dave1614..'),
            'remember_token' => Str::random(10),
            'created' => 1,
        ]);

        User::create([
            'user_name' => 'dave1614',
            'slug' =>'dave1614',
            'name' => "Nwogo David",
            'email' => "ikechukwunwogo@gmail.com",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'country_id' => 151,
            'phone' => "07051942325",
            'password' => Hash::make("Dave1614.."),
            'remember_token' => Str::random(10),
            'created' => 1,
        ]);

        User::factory(10)->create();
        $functions = new UsefulFunctions();

        $functions->giveUsersInitBusinesses();

    }
}
