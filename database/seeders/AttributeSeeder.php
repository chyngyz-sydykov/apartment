<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::factory()->create([
            'name' => 'wifi',
        ]);

        Attribute::factory()->create([
            'name' => 'tv',
        ]);

        Attribute::factory()->create([
            'name' => 'heating',
        ]);
    }
}
