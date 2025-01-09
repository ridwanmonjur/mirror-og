<?php

namespace App\Filament\Resources\ImageVideoResource\Pages;

use App\Filament\Resources\ImageVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImageVideo extends EditRecord
{
    protected static string $resource = ImageVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
