<?php

namespace App\Filament\Resources\EventTierResource\Pages;

use App\Filament\Resources\EventTierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventTiers extends ListRecords
{
    protected static string $resource = EventTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
