<?php

namespace App\Filament\Resources\TeamProfileResource\Pages;

use App\Filament\Resources\TeamProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamProfile extends EditRecord
{
    protected static string $resource = TeamProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
