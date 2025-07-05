<?php

namespace App\Filament\Resources\EventTierPrizeResource\Pages;

use App\Filament\Resources\EventTierPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventTierPrize extends EditRecord
{
    protected static string $resource = EventTierPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
