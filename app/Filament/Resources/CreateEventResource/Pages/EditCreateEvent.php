<?php

namespace App\Filament\Resources\CreateEventResource\Pages;

use App\Filament\Resources\CreateEventResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreateEvent extends EditRecord
{
    protected static string $resource = CreateEventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
