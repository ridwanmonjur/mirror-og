<?php

namespace App\Filament\Resources\StarsResource\Pages;

use App\Filament\Resources\StarsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStars extends EditRecord
{
    protected static string $resource = StarsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
