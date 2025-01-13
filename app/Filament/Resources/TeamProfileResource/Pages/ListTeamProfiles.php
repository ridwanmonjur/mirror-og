<?php

namespace App\Filament\Resources\TeamProfileResource\Pages;

use App\Filament\Resources\TeamProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamProfiles extends ListRecords
{
    protected static string $resource = TeamProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
