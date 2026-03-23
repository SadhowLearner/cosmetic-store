<?php

namespace App\Filament\Resources\Cosmetics\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CosmeticInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Preview')
                    ->schema([
                        ImageEntry::make('thumbnail')
                            ->imageHeight(250)
                            ->extraImgAttributes(['class' => 'rounded-xl shadow object-cover']),
                    ])
                    ->columnSpan(3),

                
                Section::make('Basic Information')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('name')->weight('bold'),
                        TextEntry::make('slug')->color('gray'),

                        TextEntry::make('brand.name'),
                        TextEntry::make('category.name'),
                    ])
                    ->columnSpan(2),

                
                Section::make('Pricing & Stock')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('price')->money('IDR'),
                        TextEntry::make('stock')->numeric(),
                        IconEntry::make('is_popular')
                            ->boolean()
                            ->true(Heroicon::OutlinedCheck, color: 'success')
                            ->false(Heroicon::OutlinedXCircle, color: 'danger'),
                    ])
                    ->columnSpan(2),

                Section::make('Details')
                    ->schema([
                        TextEntry::make('about')
                            ->markdown()
                            ->placeholder('-'),
                    ])
                    ->collapsible()
                    ->columnSpan(3),

                Section::make('Meta')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),

                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn($record) => $record->trashed()),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),
            ])
            ->columns(5);
    }
}
