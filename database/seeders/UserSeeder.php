<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
