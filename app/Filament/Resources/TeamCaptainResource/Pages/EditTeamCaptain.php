<?php

namespace App\Filament\Resources\TeamCaptainResource\Pages;

use App\Filament\Resources\TeamCaptainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamCaptain extends EditRecord
{
    protected static string $resource = TeamCaptainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
