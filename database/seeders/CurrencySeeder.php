<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$'],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$'],
            ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$'],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF'],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥'],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥'],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹'],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R'],
            ['code' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh'],
            ['code' => 'GHS', 'name' => 'Ghanaian Cedi', 'symbol' => '₵'],
            ['code' => 'UGX', 'name' => 'Ugandan Shilling', 'symbol' => 'USh'],
            ['code' => 'XOF', 'name' => 'West African CFA Franc', 'symbol' => 'CFA'],
            ['code' => 'XAF', 'name' => 'Central African CFA Franc', 'symbol' => 'FCFA'],
            ['code' => 'MAD', 'name' => 'Moroccan Dirham', 'symbol' => 'د.م'],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼'],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ'],
            ['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => '﷼'],
            ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'symbol' => 'د.ك'],
            ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'symbol' => '.د.ب'],
            ['code' => 'OMR', 'name' => 'Omani Rial', 'symbol' => '﷼'],
            ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨'],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳'],
            ['code' => 'THB', 'name' => 'Thai Baht', 'symbol' => '฿'],
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp'],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$'],
            ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱'],
            ['code' => 'VND', 'name' => 'Vietnamese Dong', 'symbol' => '₫'],
            ['code' => 'ARS', 'name' => 'Argentine Peso', 'symbol' => '$'],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$'],
            ['code' => 'CLP', 'name' => 'Chilean Peso', 'symbol' => '$'],
            ['code' => 'COP', 'name' => 'Colombian Peso', 'symbol' => '$'],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$'],
            ['code' => 'PEN', 'name' => 'Peruvian Sol', 'symbol' => 'S/'],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽'],
            ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺'],
            ['code' => 'PLN', 'name' => 'Polish Zloty', 'symbol' => 'zł'],
            ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr'],
            ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr'],
            ['code' => 'DKK', 'name' => 'Danish Krone', 'symbol' => 'kr'],
            ['code' => 'CZK', 'name' => 'Czech Koruna', 'symbol' => 'Kč'],
            ['code' => 'HUF', 'name' => 'Hungarian Forint', 'symbol' => 'Ft'],
            ['code' => 'ILS', 'name' => 'Israeli Shekel', 'symbol' => '₪'],
            ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩'],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(['code' => $currency['code']], $currency);
        }
    }
}
