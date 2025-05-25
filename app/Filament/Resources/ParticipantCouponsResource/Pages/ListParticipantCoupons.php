<?php

namespace App\Filament\Resources\ParticipantCouponsResource\Pages;

use App\Filament\Resources\ParticipantCouponsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParticipantCoupons extends ListRecords
{
    protected static string $resource = ParticipantCouponsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
