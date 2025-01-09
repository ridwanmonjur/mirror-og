<?php

namespace App\Filament\Resources\EventInvitationResource\Pages;

use App\Filament\Resources\EventInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventInvitations extends ListRecords
{
    protected static string $resource = EventInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
