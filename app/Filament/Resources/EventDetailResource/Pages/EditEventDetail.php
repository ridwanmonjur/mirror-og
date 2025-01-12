<?php

namespace App\Filament\Resources\EventDetailResource\Pages;

use App\Filament\Resources\EventDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventDetail extends EditRecord
{
    protected static string $resource = EventDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
