<?php

namespace App\Filament\Resources\Cosmetics\Pages;

use App\Filament\Resources\Cosmetics\CosmeticResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCosmetic extends ViewRecord
{
    protected static string $resource = CosmeticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
