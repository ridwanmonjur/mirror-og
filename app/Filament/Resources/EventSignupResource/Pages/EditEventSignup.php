<?php

namespace App\Filament\Resources\EventSignupResource\Pages;

use App\Filament\Resources\EventSignupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventSignup extends EditRecord
{
    protected static string $resource = EventSignupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
