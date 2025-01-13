<?php

namespace App\Filament\Resources\TeamFollowResource\Pages;

use App\Filament\Resources\TeamFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamFollow extends EditRecord
{
    protected static string $resource = TeamFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
