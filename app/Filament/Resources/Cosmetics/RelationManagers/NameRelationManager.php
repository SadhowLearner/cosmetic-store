<?php

namespace App\Filament\Resources\Cosmetics\RelationManagers;

use App\Filament\Resources\Cosmetics\CosmeticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class NameRelationManager extends RelationManager
{
    protected static string $relationship = 'name';

    protected static ?string $relatedResource = CosmeticResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
