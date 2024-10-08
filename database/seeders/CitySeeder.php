<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::factory()->create([
            'name' => 'Bishkek',
        ]);

        City::factory()->create([
            'name' => 'Osh',
        ]);

        City::factory()->create([
            'name' => 'Almaty',
        ]);
    }
}
