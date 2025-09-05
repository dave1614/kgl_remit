<?php

namespace Database\Factories;

use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    public function definition(): array
    {
        // Get all currency IDs from DB
        $currencyIds = Currency::pluck('id')->toArray();

        // Safety fallback: if not enough currencies, create some
        if (count($currencyIds) < 2) {
            // Create 5 default currencies if DB empty
            $defaultCurrencies = [
                ['code' => 'USD', 'name' => 'US Dollar'],
                ['code' => 'EUR', 'name' => 'Euro'],
                ['code' => 'GBP', 'name' => 'British Pound'],
                ['code' => 'NGN', 'name' => 'Nigerian Naira'],
                ['code' => 'KES', 'name' => 'Kenyan Shilling'],
            ];

            foreach ($defaultCurrencies as $c) {
                Currency::firstOrCreate(['code' => $c['code']], $c);
            }

            $currencyIds = Currency::pluck('id')->toArray();
        }

        // Pick two different IDs
        $fromId = $this->faker->randomElement($currencyIds);
        do {
            $toId = $this->faker->randomElement($currencyIds);
        } while ($fromId === $toId);

        return [
            'from_currency_id' => $fromId,
            'to_currency_id'   => $toId,
            'rate'             => $this->faker->randomFloat(6, 0.1, 2500), // high precision
            'note'             => $this->faker->optional()->sentence(),
        ];
    }
}
