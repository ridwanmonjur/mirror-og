<?php

namespace App\Filament\Resources\CreateEventResource\Pages;

use App\Filament\Resources\CreateEventResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreateEvents extends ListRecords
{
    protected static string $resource = CreateEventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
