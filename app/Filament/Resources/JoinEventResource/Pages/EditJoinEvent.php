<?php

namespace App\Filament\Resources\JoinEventResource\Pages;

use App\Filament\Resources\JoinEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJoinEvent extends EditRecord
{
    protected static string $resource = JoinEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
