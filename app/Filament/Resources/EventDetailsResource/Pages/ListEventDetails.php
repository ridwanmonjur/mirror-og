<?php

namespace App\Filament\Resources\EventDetailsResource\Pages;

use App\Filament\Resources\EventDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventDetails extends ListRecords
{
    protected static string $resource = EventDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
