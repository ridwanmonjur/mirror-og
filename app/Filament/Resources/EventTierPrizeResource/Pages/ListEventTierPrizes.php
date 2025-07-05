<?php

namespace App\Filament\Resources\EventTierPrizeResource\Pages;

use App\Filament\Resources\EventTierPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventTierPrizes extends ListRecords
{
    protected static string $resource = EventTierPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
