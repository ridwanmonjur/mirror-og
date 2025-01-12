<?php

namespace App\Filament\Resources\EventDetailResource\Pages;

use App\Filament\Resources\EventDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventDetails extends ListRecords
{
    protected static string $resource = EventDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
