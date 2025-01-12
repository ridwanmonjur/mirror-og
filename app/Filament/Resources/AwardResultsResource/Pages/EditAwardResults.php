<?php

namespace App\Filament\Resources\AwardResultsResource\Pages;

use App\Filament\Resources\AwardResultsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwardResults extends EditRecord
{
    protected static string $resource = AwardResultsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
