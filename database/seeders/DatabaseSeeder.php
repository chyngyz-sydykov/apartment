<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CitySeeder::class,
            ApartmentSeeder::class,
            AttributeSeeder::class,
            ImagesSeeder::class,
            RoleAndPermissionSeeder::class,
        ]);


        // User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ]);

        $manager = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@admin.com',
        ]);

        $admin->assignRole('admin');
        $manager->assignRole('manager');
    }
}
