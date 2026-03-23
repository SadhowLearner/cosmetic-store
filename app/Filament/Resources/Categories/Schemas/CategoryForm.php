<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                FileUpload::make('photo')
                    ->columnSpanFull()
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->avatar()
                    ->circleCropper()
            ]);
    }
}
