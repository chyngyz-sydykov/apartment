<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\CityResource;
use App\Models\City;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CityResourceTest extends TestCase
{
    use RefreshDatabase;

    public const COLUMN_NAME = 'name';
    public const CITIES_TABLE_NAME = 'cities';

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

    public function test_can_access_cities_page_as_admin_role_user(): void
    {
        $cityName = fake()->randomAscii();
        City::factory()->create([self::COLUMN_NAME => $cityName]);

        Livewire::test(CityResource\Pages\ListCities::class)
            ->assertCanRenderTableColumn(self::COLUMN_NAME)
            ->assertCanSeeTableRecords(City::where(self::COLUMN_NAME, $cityName)->get())
            ->assertSuccessful();
    }

    public function test_can_create_city_as_admin_role_user(): void
    {
        // act
        $cityName = fake()->randomAscii();
        Livewire::test(CityResource\Pages\CreateCity::class)
            ->fillForm([
                self::COLUMN_NAME => $cityName
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // assert
        $this->assertDatabaseHas(self::CITIES_TABLE_NAME, [
            self::COLUMN_NAME => $cityName
        ]);
    }

    public function test_cannot_create_city_with_blank_name_as_admin_role_user(): void
    {
        // act
        Livewire::test(CityResource\Pages\CreateCity::class)
            ->fillForm([
                self::COLUMN_NAME => ''
            ])
            ->call('create')
            ->assertHasFormErrors();

        // assert
        $this->assertDatabaseMissing(self::CITIES_TABLE_NAME, [
            self::COLUMN_NAME => ''
        ]);
    }

    public function test_can_update_city_as_admin_role_user(): void
    {
        // arrange
        $newCityName = fake()->randomAscii();
        $oldCityName = fake()->randomAscii();
        $city = City::factory()->create([self::COLUMN_NAME => $oldCityName]);

        // act
        Livewire::test(CityResource\Pages\EditCity::class, ['record' => $city->getKey()])
            ->assertFormSet([
                self::COLUMN_NAME => $oldCityName
            ])
            ->fillForm([
                self::COLUMN_NAME => $newCityName
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // assert
        $this->assertDatabaseMissing(self::CITIES_TABLE_NAME, [
            self::COLUMN_NAME => $oldCityName
        ]);
        $this->assertDatabaseHas(self::CITIES_TABLE_NAME, [
            self::COLUMN_NAME => $newCityName
        ]);
    }



    public function test_can_delete_city_from_index_page_as_admin_role_user(): void
    {
        // arrange
        $cityName = fake()->randomAscii();
        $city = City::factory()->create([self::COLUMN_NAME => $cityName]);

        // act
        Livewire::test(CityResource\Pages\ListCities::class)
            ->callTableAction(DeleteAction::class, $city);

        // assert
        $this->assertModelMissing($city);
        $this->assertDatabaseMissing(self::CITIES_TABLE_NAME, [
            self::COLUMN_NAME => $cityName
        ]);
    }
}
