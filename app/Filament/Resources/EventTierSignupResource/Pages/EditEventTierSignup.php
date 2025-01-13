<?php

namespace App\Filament\Resources\EventTierSignupResource\Pages;

use App\Filament\Resources\EventTierSignupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventTierSignup extends EditRecord
{
    protected static string $resource = EventTierSignupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
