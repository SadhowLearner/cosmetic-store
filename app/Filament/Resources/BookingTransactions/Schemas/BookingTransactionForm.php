<?php

namespace App\Filament\Resources\BookingTransactions\Schemas;

use App\Models\Cosmetic;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
// use Filament\Support\RawJs;

class BookingTransactionForm
{
    public static function updateSubTotal($state, callable $set, $get)
    {
        $cosmetic = \App\Models\Cosmetic::find($state);
        $set('price', $cosmetic?->price ?? 0);
        $set('price_display', number_format($cosmetic?->price ?? 0, 0, ',', '.'));

        if (is_numeric($get('price'))) {
            $set('price', $get('price'));
        } else {
            $set('price', 0);
        }
        $price =  $get('price');
        $qty = $get('qty') ?? 0;

        $subTotal = $price * $qty;
        $set('sub_total', number_format($subTotal, 0, ',', '.'));
    }

    public static function updateTotals(Get $get, Set $set)
    {
        $details = collect($get('detail'));

        $cosmeticIds = $details->pluck('cosmetic_id')->filter()->unique();

        $prices = Cosmetic::whereIn('id', $cosmeticIds)->pluck('price', 'id');


        $subTotalAmount = $details->sum(function ($item) use ($prices) {
            $price = $prices->get($item['cosmetic_id'] ?? null, 0);
            $qty = is_numeric($item['qty']) ? $item['qty'] : 0;
            return $price * $qty;
        });
        $set('sub_total_amount', $subTotalAmount);

        $totalTaxAmount = round($subTotalAmount * 0.11); // Assuming a tax rate of 11%
        $set('total_tax_amount', $totalTaxAmount);

        $totalQty = $details->sum(fn($item) => is_numeric($item['qty'] ?? null) ? (int) $item['qty'] : 0);
        $set('total_qty', $totalQty);

        $totalAmount = round($subTotalAmount + $totalTaxAmount);
        $set('total_amount', number_format($totalAmount, 0, ',', '.'));
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
                            Repeater::make('detail')
                                ->relationship('detail')
                                ->columns(3)
                                ->live()

                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    self::updateTotals($get, $set);
                                })
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
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateSubTotal($get('cosmetic_id'), $set, $get);
                                            })
                                            ->default(0)
                                            ->minValue(0)
                                            ->dehydrateStateUsing(fn($state) => (int) ($state ?: 0)),
                                        TextInput::make('price_display')
                                            ->label('Cosmetic Price')
                                            ->readOnly()
                                            ->default('0')
                                            ->dehydrated(false)
                                            ->prefix('IDR'),
                                        Hidden::make('price')
                                            ->required(),
                                        TextInput::make('sub_total')
                                            ->label('Sub Total')
                                            ->readOnly()
                                            ->dehydrated(false)
                                            ->prefix('IDR')
                                            ->default('0')
                                            ->columnSpanFull(),
                                    ]
                                ),

                            Flex::make([
                                TextInput::make('sub_total_amount_display')
                                    ->label('Sub Total Amount')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->reactive()
                                    ->prefix('IDR')
                                    ->default('0'),
                                TextInput::make('total_tax_amount_display')
                                    ->label('Total Tax Amount (11%)')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->reactive()
                                    ->prefix('IDR')
                                    ->default('0'),
                                TextInput::make('total_amount_display')
                                    ->label('Total Amount')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->reactive()
                                    ->prefix('IDR')
                                    ->default('0'),
                                TextInput::make('total_qty')
                                    ->label('Total Quantity')
                                    ->readOnly()
                                    ->default(0),
                                Hidden::make('sub_total_amount')->required(),
                                Hidden::make('total_tax_amount')->required(),
                                Hidden::make('total_amount')->required(),
                            ])->columns(4)->columnSpanFull(),

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
