<?php

namespace App\Filament\Resources\RosterMemberResource\Pages;

use App\Filament\Resources\RosterMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRosterMembers extends ListRecords
{
    protected static string $resource = RosterMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
