<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentResource\Pages;
use App\Filament\Resources\ApartmentResource\RelationManagers\AttributesRelationManager;
use App\Models\Apartment;
use App\Models\City;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApartmentResource extends Resource
{
    public const ROOM_NUMBER_LIST = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 10];
    protected static ?string $model = Apartment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Split::make([
                        Section::make([
                            Section::make([
                                TextInput::make('area')->integer()->required(),
                                TextInput::make('price')->numeric()->default(0)->required(),
                                Select::make('room_number')->options(self::ROOM_NUMBER_LIST)->required(),
                            ])->columns(3),
                            TextInput::make('address')->required(),
                            MarkdownEditor::make('description'),
                            FileUpload::make('image_ids')->multiple(),
                        ]),
                        Section::make([
                            Select::make('user_id')
                                ->label('Owner')
                                ->required()
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('city_id')
                                ->label('City')
                                ->required()
                                ->options(City::all()->pluck('name', 'id')),
                            Toggle::make('is_active')
                                ->label('Is active')
                                ->default(false)
                        ])->columns(1)->grow(false),
                    ])->from('md')
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('area')->sortable(),
                TextColumn::make('room_number')->sortable(),
                TextColumn::make('address')->searchable(),
                TextColumn::make('price')->sortable(),
                TextColumn::make('city.name'),
                TextColumn::make('owner.name')->searchable(),
                CheckboxColumn::make('is_active')->disabled(),
                TextColumn::make('updated_at')->sortable()->dateTime('j/M/Y H:i'),
            ])
            ->filters([
                QueryBuilder::make('updated_at')
                    ->constraints([
                        DateConstraint::make('updated_at'),
                    ]),
                SelectFilter::make('city_id')
                    ->label('City ')
                    ->options(
                        City::all()->pluck('name', 'id')
                    ),
                SelectFilter::make('user_id')
                    ->label('Owner')
                    ->options(
                        User::all()->pluck('name', 'id')
                    ),
                SelectFilter::make('room_number')
                    ->options(self::ROOM_NUMBER_LIST)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttributesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApartments::route('/'),
            'create' => Pages\CreateApartment::route('/create'),
            'edit' => Pages\EditApartment::route('/{record}/edit'),
        ];
    }
}
