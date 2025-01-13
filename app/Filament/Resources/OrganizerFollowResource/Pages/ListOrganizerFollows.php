<?php

namespace App\Filament\Resources\OrganizerFollowResource\Pages;

use App\Filament\Resources\OrganizerFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizerFollows extends ListRecords
{
    protected static string $resource = OrganizerFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
