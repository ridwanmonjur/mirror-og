<?php

namespace App\Filament\Resources\UserDiscountResource\Pages;

use App\Filament\Resources\UserDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserDiscounts extends ListRecords
{
    protected static string $resource = UserDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
