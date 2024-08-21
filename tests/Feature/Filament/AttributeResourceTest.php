<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\AttributesResource\Pages\CreateAttributes;
use App\Filament\Resources\AttributesResource\Pages\EditAttributes;
use App\Filament\Resources\AttributesResource\Pages\ListAttributes;
use App\Models\Attribute;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class AttributeResourceTest extends TestCase
{
    use RefreshDatabase;

    public const COLUMN_NAME = 'name';
    public const ATTRIBUTES_TABLE_NAME = 'attributes';

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

    #[group('salam')]
    public function test_can_access_attributes_page_as_admin_role_user(): void
    {
        $attributeName = fake()->randomAscii();
        Attribute::factory()->create([self::COLUMN_NAME => $attributeName]);

        Livewire::test(ListAttributes::class)
            ->assertCanRenderTableColumn(self::COLUMN_NAME)
            ->assertCanSeeTableRecords(Attribute::where(self::COLUMN_NAME, $attributeName)->get())
            ->assertSuccessful();
    }

    public function test_can_create_attribute_as_admin_role_user(): void
    {
        // act
        $attributeName = fake()->randomAscii();
        Livewire::test(CreateAttributes::class)
            ->fillForm([
                self::COLUMN_NAME => $attributeName
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // assert
        $this->assertDatabaseHas(self::ATTRIBUTES_TABLE_NAME, [
            self::COLUMN_NAME => $attributeName
        ]);
    }

    public function test_cannot_create_attribute_with_blank_name_as_admin_role_user(): void
    {
        // act
        Livewire::test(CreateAttributes::class)
            ->fillForm([
                self::COLUMN_NAME => ''
            ])
            ->call('create')
            ->assertHasFormErrors();

        // assert
        $this->assertDatabaseMissing(self::ATTRIBUTES_TABLE_NAME, [
            self::COLUMN_NAME => ''
        ]);
    }

    public function test_can_update_attribute_as_admin_role_user(): void
    {
        // arrange
        $newAttributeName = fake()->randomAscii();
        $oldAttributeName = fake()->randomAscii();
        $attribute = Attribute::factory()->create([self::COLUMN_NAME => $oldAttributeName]);

        // act
        Livewire::test(EditAttributes::class, ['record' => $attribute->getKey()])
            ->assertFormSet([
                self::COLUMN_NAME => $oldAttributeName
            ])
            ->fillForm([
                self::COLUMN_NAME => $newAttributeName
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // assert
        $this->assertDatabaseMissing(self::ATTRIBUTES_TABLE_NAME, [
            self::COLUMN_NAME => $oldAttributeName
        ]);
        $this->assertDatabaseHas(self::ATTRIBUTES_TABLE_NAME, [
            self::COLUMN_NAME => $newAttributeName
        ]);
    }

    public function test_can_delete_attribute_from_index_page_as_admin_role_user(): void
    {
        // arrange
        $attributeName = fake()->randomAscii();
        $attribute = Attribute::factory()->create([self::COLUMN_NAME => $attributeName]);

        // act
        Livewire::test(ListAttributes::class)
            ->callTableAction(DeleteAction::class, $attribute);

        // assert
        $this->assertModelMissing($attribute);
        $this->assertDatabaseMissing(self::ATTRIBUTES_TABLE_NAME, [
            self::COLUMN_NAME => $attributeName
        ]);
    }
}
