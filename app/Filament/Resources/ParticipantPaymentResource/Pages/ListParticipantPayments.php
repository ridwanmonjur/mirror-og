<?php

namespace App\Filament\Resources\ParticipantPaymentResource\Pages;

use App\Filament\Resources\ParticipantPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParticipantPayments extends ListRecords
{
    protected static string $resource = ParticipantPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
