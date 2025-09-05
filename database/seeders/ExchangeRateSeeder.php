<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 20 random realistic exchange pairs
        ExchangeRate::factory()->count(20)->create();
    }
}
