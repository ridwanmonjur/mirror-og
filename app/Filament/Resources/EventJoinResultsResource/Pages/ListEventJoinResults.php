<?php

namespace App\Filament\Resources\EventJoinResultsResource\Pages;

use App\Filament\Resources\EventJoinResultsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventJoinResults extends ListRecords
{
    protected static string $resource = EventJoinResultsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
