<?php

namespace App\Filament\Resources\EventDetailResource\Pages;

use App\Filament\Resources\EventDetailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventDetail extends EditRecord
{
    protected static string $resource = EventDetailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
