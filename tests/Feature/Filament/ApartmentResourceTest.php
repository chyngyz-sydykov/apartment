<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\ApartmentResource\Pages\ListApartments;
use App\Filament\Resources\ApartmentResource\Pages\CreateApartment;
use App\Filament\Resources\ApartmentResource\Pages\EditApartment;
use App\Models\Apartment;
use App\Models\City;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ApartmentResourceTest extends TestCase
{
    use RefreshDatabase;

    public const COLUMN_NAME = 'name';
    public const APARTMENTS_TABLE_NAME = 'apartments';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        // arrange
        $testAdmin = User::factory()->create([
            self::COLUMN_NAME => 'Test admin',
            'email' => 'test@admin.com',
        ]);
        $testAdmin->assignRole('admin');

        $this->actingAs($testAdmin);
    }

    public function test_can_view_and_filter_apartments_page_as_admin_role_user(): void
    {
        City::factory()->count(3)->create();
        Apartment::factory()->count(5)->create();

        $randomCity = City::all()->random();

        Livewire::test(ListApartments::class)
            ->assertCanRenderTableColumn('area')
            ->assertCanRenderTableColumn('room_number')
            ->assertCanRenderTableColumn('price')
            ->assertCanRenderTableColumn('is_active')
            ->assertCanRenderTableColumn('owner.name')
            ->assertCanRenderTableColumn('city.name')
            ->filterTable('city_id', $randomCity->getKey())
            ->assertCanSeeTableRecords(Apartment::where('city_id', $randomCity->getKey())->get())
            ->assertSuccessful();
    }

    public function test_cannot_create_apartment_with_empty_fields_as_admin_role_user(): void
    {
        Livewire::test(CreateApartment::class)
            ->fillForm([
                'area' => null,
                'room_number' => null,
                'price' => null,
                'address' => null,
                'user_id' => null,
                'city_id' => null,
            ])
            ->call('create')
            ->assertHasErrors([
                'data.area' => [
                    "The area field is required."
                ],
                'data.price' => [
                    "The price field is required."
                ],
                'data.room_number' => [
                    "The room number field is required."
                ],
                'data.address' => [
                    "The address field is required."
                ],
                'data.user_id' => [
                    "The owner field is required."
                ],
                'data.city_id' => [
                    "The city field is required."
                ],
            ]);
    }

    public function test_cannot_create_apartment_with_invalid_fields_as_admin_role_user(): void
    {
        $fakeAddress = fake()->address();
        Livewire::test(CreateApartment::class)
            ->fillForm([
                'area' => "should be fff",
                'room_number' => 1,
                'price' => "should be number",
                'address' => $fakeAddress,
                'user_id' => 10,
                'city_id' => 10,
            ])
            ->call('create')
            ->assertHasErrors([
                'data.area' => [
                    'The area field must be an integer.',
                    'The area field must be a number.',
                ],
                'data.price' => [
                    'The price field must be a number.'
                ]
            ]);

        // assert
        $this->assertDatabaseMissing(self::APARTMENTS_TABLE_NAME, [
            'address' => $fakeAddress
        ]);
    }

    public function test_cannot_create_apartment_with_not_existing_relation_in_db_as_admin_role_user(): void
    {
        $this->expectException(QueryException::class);
        Livewire::test(CreateApartment::class)
            ->fillForm([
                'area' => 999,
                'room_number' => 1,
                'price' => 11.11,
                'address' => fake()->address(),
                'user_id' => 10,
                'city_id' => 10,
            ])
            ->call('create');
    }

    public function test_can_update_apartment_as_admin_role_user(): void
    {
        // arrange
        $oldAddress = fake()->address();
        $newAddress = 'new ' . fake()->address();
        City::factory()->create();
        $apartment = Apartment::factory()->create([
            'address' => $oldAddress
        ]);

        // act
        Livewire::test(EditApartment::class, ['record' => $apartment->getKey()])
            ->assertFormSet([
                'address' => $oldAddress
            ])
            ->fillForm([
                'address' => $newAddress
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // assert
        $this->assertDatabaseMissing(self::APARTMENTS_TABLE_NAME, [
            'address' => $oldAddress
        ]);
        $this->assertDatabaseHas(self::APARTMENTS_TABLE_NAME, [
            'address' => $newAddress
        ]);
    }

    public function test_can_delete_apartment_from_apartment_list_page_as_admin_role_user(): void
    {
        // arrange
        $address = fake()->address();
        City::factory()->create();
        $apartment = Apartment::factory()->create([
            'address' => $address
        ]);

        // act
        Livewire::test(ListApartments::class)
            ->callTableAction(DeleteAction::class, $apartment);

        // assert
        $this->assertModelMissing($apartment);
        $this->assertDatabaseMissing(self::APARTMENTS_TABLE_NAME, [
            'address' => $address
        ]);
    }
}
