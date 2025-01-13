<?php

namespace App\Filament\Resources\ParticipantFollowResource\Pages;

use App\Filament\Resources\ParticipantFollowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParticipantFollows extends ListRecords
{
    protected static string $resource = ParticipantFollowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
