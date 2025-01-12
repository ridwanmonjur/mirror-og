<?php

namespace App\Filament\Resources\EventJoinResultsResource\Pages;

use App\Filament\Resources\EventJoinResultsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventJoinResults extends EditRecord
{
    protected static string $resource = EventJoinResultsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
