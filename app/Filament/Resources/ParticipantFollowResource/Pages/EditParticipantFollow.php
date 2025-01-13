<?php

namespace App\Filament\Resources\ParticipantFollowResource\Pages;

use App\Filament\Resources\ParticipantFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParticipantFollow extends EditRecord
{
    protected static string $resource = ParticipantFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
