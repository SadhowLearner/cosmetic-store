<?php

namespace App\Filament\Resources\BookingTransactions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BookingTransactionForm
{
    public static function updateSubTotal($state, callable $set, $get)
    {
        $cosmetic = \App\Models\Cosmetic::find($state);
        $set('price', $cosmetic?->price ?? 0);
        $set('price_display', number_format($cosmetic?->price ?? 0, 0, ',', '.'));
        $price = $get('price') ?? 0;
        $qty = $get('qty') ?? 0;
        $subTotal = $price * $qty;
        $set('sub_total_amount', number_format($subTotal, 0, ',', '.'));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Wizard::make([
                    Step::make('Product & Price')
                        ->completedIcon('heroicon-o-check')
                        ->description('Enter the product details and pricing information.')
                        ->schema([
                            Repeater::make('Transaction Details')
                                ->relationship('detail')
                                ->columns(3)
                                ->schema(
                                    [
                                        Select::make('cosmetic_id')
                                            ->relationship('cosmetic', 'name')
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, $get) {

                                                self::updateSubTotal($state, $set, $get);
                                            })
                                            ->searchable()
                                            ->required(),

                                        TextInput::make('qty')
                                            ->label('Quantity')
                                            ->required()
                                            ->numeric()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, $get) {

                                                self::updateSubTotal($get('cosmetic_id'), $set, $get);
                                            })
                                            ->default(1),
                                        TextInput::make('price_display')
                                            ->label('Price')
                                            ->readOnly()
                                            ->default('0')
                                            ->dehydrated(false)
                                            ->prefix('IDR'),
                                        Hidden::make('price')
                                            ->required(),
                                        TextInput::make('sub_total_amount')
                                            ->label('Sub Total')
                                            ->readOnly()
                                            ->dehydrated(false)
                                            ->prefix('IDR')
                                            ->default('0')
                                            ->columnSpanFull()
                                    ]
                                ),
                            Flex::make([
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->readOnly()
                                    ->prefix('IDR')
                                    ->default('0'),
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->readOnly()
                                    ->prefix('IDR')
                                    ->default('0'),
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->readOnly()
                                    ->prefix('IDR')
                                    ->default('0'),
                                TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->readOnly()
                                    ->prefix('IDR')
                                    ->default('0'),
                            ])
                                ->columns(4)
                                ->columnSpanFull(),
                        ]),
                    Step::make('Customer Information')
                        ->completedIcon('heroicon-o-check')
                        ->description('Enter customer information.')
                        ->schema([
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('phone')
                                ->prefixIcon(Heroicon::OutlinedPhone)
                                ->required(),
                            TextInput::make('email')
                                ->email()
                                ->prefixIcon(Heroicon::OutlinedEnvelope)
                                ->required(),

                        ]),
                    Step::make('Delivery Information')
                        ->completedIcon('heroicon-o-check')
                        ->description('Delivery information.')
                        ->schema([
                            TextInput::make('city')->required(),
                            TextInput::make('post_code')->required(),
                            Textarea::make('address')->required(),
                        ]),
                    Step::make('Payment Information')
                        ->completedIcon('heroicon-o-check')
                        ->description('Payment information')
                        ->schema([
                            TextInput::make('booking_trx_id')
                                ->required()
                                ->label('Booking Transaction ID'),
                            ToggleButtons::make('is_paid')
                                ->label('Payment Status')
                                ->boolean()
                                ->grouped()
                                ->icons([
                                    true => Heroicon::OutlinedPencil,
                                    false => Heroicon::OutlinedClock
                                ])
                                ->required(),
                            FileUpload::make('proof')
                                ->columnSpanFull()
                                ->label('Proof of Payment')
                                ->image(),
                            
                        ])
                ])
                    ->skippable()
                    ->columns(1)
                    ->columnSpanFull(),

                // TextInput::make('booking_trx_id')
                //     ->required(),
                // TextInput::make('name')
                //     ->required(),
                // TextInput::make('email')
                //     ->label('Email address')
                //     ->email(),
                // TextInput::make('phone')
                //     ->tel(),
                // TextInput::make('proof'),
                // TextInput::make('post_code'),
                // TextInput::make('city'),
                // Textarea::make('address')
                //     ->required()
                //     ->columnSpanFull(),
                // TextInput::make('qty')
                //     ->required()
                //     ->numeric()
                //     ->default(0),
                // TextInput::make('sub_total_amount')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('total_amount')
                //     ->required()
                //     ->numeric(),
                // TextInput::make('total_tax_amount')
                //     ->required()
                //     ->numeric(),
                // Toggle::make('is_paid')
                //     ->required(),
            ]);
    }
}
