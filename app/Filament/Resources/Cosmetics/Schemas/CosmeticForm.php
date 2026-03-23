<?php

namespace App\Filament\Resources\Cosmetics\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class CosmeticForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Detail')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder(0)
                            ->prefix('IDR'),
                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->placeholder(0)
                            ->step(1)
                            ->prefix('Qtys'),
                        FileUpload::make('thumbnail')
                            ->required(),
                    ]),
                Fieldset::make('Additional Information')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->required()
                            ->searchable(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable(),
                        Repeater::make('photos')
                            ->relationship('photos')
                            ->schema([
                                FileUpload::make('photo')
                                    ->required()
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatioOptions(['1:1', '4:3', '16:9', null]),
                            ]),
                        Repeater::make('benefits')
                            ->relationship('benefits')
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                            ]),
                        Select::make('is_popular')
                            ->options([
                                true => 'Popular',
                                false => 'Not Popular',
                            ])
                            ->required(),
                        Textarea::make('about')
                            ->columnSpanFull()
                    ])
            ]);
    }
}
