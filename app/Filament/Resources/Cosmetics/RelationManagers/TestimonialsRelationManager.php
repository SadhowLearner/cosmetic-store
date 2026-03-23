<?php

namespace App\Filament\Resources\Cosmetics\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialsRelationManager extends RelationManager
{
    protected static string $relationship = 'testimonials';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('rating')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('message')
                    ->sortable()
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(5)
                    ->step(0.1),
                FileUpload::make('photo')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions(['1:1', '4:3', '16:9', null]),
                Textarea::make('message')
                    ->required(),
            ]);
    }
}
