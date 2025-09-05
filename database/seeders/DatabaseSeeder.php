<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([

            CountrySeeder::class,
            RegionSeeder::class,
            StateSeeder::class,
            InecStateSeeder::class,
            InecLgaSeeder::class,
            InecWardSeeder::class,
            CitySeeder::class,
            CurrencySeeder::class,
            UserSeeder::class,
        ]);

    }
}
