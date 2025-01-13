<?php

namespace App\Filament\Resources\UserDiscountResource\Pages;

use App\Filament\Resources\UserDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserDiscount extends EditRecord
{
    protected static string $resource = UserDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
