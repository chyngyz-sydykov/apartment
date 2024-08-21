<?php

declare(strict_types=1);

namespace App\Filament\Resources\AttributesResource\Pages;

use App\Filament\Resources\AttributesResource;
use Filament\Resources\Pages\EditRecord;

class EditAttributes extends EditRecord
{
    protected static string $resource = AttributesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
