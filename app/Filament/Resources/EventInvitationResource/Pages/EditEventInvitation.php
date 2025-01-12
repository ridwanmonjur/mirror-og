<?php

namespace App\Filament\Resources\EventInvitationResource\Pages;

use App\Filament\Resources\EventInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventInvitation extends EditRecord
{
    protected static string $resource = EventInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
