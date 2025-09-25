<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'invoice_expiry_minutes' => 30,
            'bank_name'              => 'Providus Bank',
            'account_number'         => '0000000000',
            'account_name'           => 'KGL Remit Group Limited',
            // add booleans as needed
            // 'send_email_notifications' => true,
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
