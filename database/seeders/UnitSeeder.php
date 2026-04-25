<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Meter', 'symbol' => 'm'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Roll', 'symbol' => 'roll'],
            ['name' => 'Liter', 'symbol' => 'ltr'],
            ['name' => 'Dozen', 'symbol' => 'dz'],
            ['name' => 'Pack', 'symbol' => 'pk'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['symbol' => $unit['symbol']],
                ['name' => $unit['name']]
            );
        }
    }
}
