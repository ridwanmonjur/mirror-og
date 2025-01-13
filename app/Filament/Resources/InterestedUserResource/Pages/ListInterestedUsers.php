<?php

namespace App\Filament\Resources\InterestedUserResource\Pages;

use App\Filament\Resources\InterestedUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterestedUsers extends ListRecords
{
    protected static string $resource = InterestedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
