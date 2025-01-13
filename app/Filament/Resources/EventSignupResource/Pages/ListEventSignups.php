<?php

namespace App\Filament\Resources\EventSignupResource\Pages;

use App\Filament\Resources\EventSignupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventSignups extends ListRecords
{
    protected static string $resource = EventSignupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
