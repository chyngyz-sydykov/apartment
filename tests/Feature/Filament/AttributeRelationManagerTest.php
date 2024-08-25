<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\ApartmentResource;
use App\Filament\Resources\ApartmentResource\Pages\EditApartment;
use App\Filament\Resources\ApartmentResource\RelationManagers\AttributesRelationManager;
use App\Filament\Resources\AttributesResource\Pages\CreateAttributes;
use App\Models\Apartment;
use App\Models\City;
use App\Models\User;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class AttributeRelationManagerTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AttributeSeeder::class);
        // arrange
        $testAdmin = User::factory()->create([
            'name' => 'Test admin',
            'email' => 'test@admin.com',
        ]);
        $testAdmin->assignRole('admin');

        $this->actingAs($testAdmin);
    }
    /**
     * A basic feature test example.
     */
    #[group('salam')]
    public function test_can_update_apartment_as_admin_role_user(): void
    {
        // arrange
        $address = fake()->address();
        City::factory()->create();
        $apartment = Apartment::factory()->create([
            'address' => $address
        ]);
        DB::table('apartment_attribute')->insert(['apartment_id' => $apartment->getKey(), 'attribute_id' => 1]);
        // act

        Livewire::test(AttributesRelationManager::class, [
            'ownerRecord' => $apartment,
            'pageClass' => EditApartment::class,
        ])
        ->assertCountTableRecords(1)
        ->assertTableActionHasUrl('New attribute', CreateAttributes::getUrl());
    }
}
