<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('store_name', 'S.K Trade & Suppliers');
        Setting::set('store_address', 'Your Address Here');
        Setting::set('store_phone', 'Your Phone Number');
        Setting::set('opening_balance_date', now()->startOfYear()->toDateString());
        Setting::set('opening_balance_amount', '0');
        Setting::set('currency_symbol', 'Rs.');
        Setting::set('currency_position', 'left');
        Setting::set('currency_fraction_digits', '2');
        Setting::set('currency_thousand_separator', ',');
        Setting::set('currency_decimal_separator', '.');
    }
}
