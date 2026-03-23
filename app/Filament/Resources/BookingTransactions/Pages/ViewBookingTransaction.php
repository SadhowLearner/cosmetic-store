<?php

namespace App\Filament\Resources\BookingTransactions\Pages;

use App\Filament\Resources\BookingTransactions\BookingTransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBookingTransaction extends ViewRecord
{
    protected static string $resource = BookingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
