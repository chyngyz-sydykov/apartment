<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_cannot_access_admin_panel_and_redirected_to_login_page_without_logging_in(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
        $response->assertRedirect('admin/login');
    }

    public function test_can_access_filament_admin_panel_as_admin_role_user(): void
    {
        // arrange
        $testAdmin = User::factory()->create([
            'name' => 'Test admin',
            'email' => 'test@admin.com',
        ]);

        $testAdmin->assignRole('admin');

        $this->actingAs($testAdmin);

        // act
        $response = $this->get('/admin');

        // assert
        $response->assertStatus(200);
    }

    public function test_cannot_access_filament_admin_panel_as_client_role_user(): void
    {
        // arrange
        $testClient = User::factory()->create([
            'name' => 'Test client',
            'email' => 'client@admin.com',
        ]);

        $testClient->assignRole('client');

        $this->actingAs($testClient);

        // act
        $response = $this->get('/admin');

        // assert
        $response->assertStatus(403);
    }
}
