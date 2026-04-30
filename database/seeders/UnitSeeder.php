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
            ['name' => 'Pieces',    'symbol' => 'pcs'],
            ['name' => 'Kilogram',  'symbol' => 'kg'],
            ['name' => 'Gram',      'symbol' => 'g'],
            ['name' => 'Liter',     'symbol' => 'ltr'],
            ['name' => 'Meter',     'symbol' => 'm'],
            ['name' => 'Box',       'symbol' => 'box'],
            ['name' => 'Pack',      'symbol' => 'pack'],
            ['name' => 'Roll',      'symbol' => 'roll'],
            ['name' => 'Dozen',     'symbol' => 'dz'],
            ['name' => 'Bag',       'symbol' => 'bag'],
            ['name' => 'Carton',    'symbol' => 'ctn'],
            ['name' => 'Bottle',    'symbol' => 'btl'],
            ['name' => 'Set',       'symbol' => 'set'],
            ['name' => 'Pair',      'symbol' => 'pair'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['symbol' => $unit['symbol']],
                ['name'   => $unit['name']]
            );
        }
    }
}
