<?php

namespace App\Filament\Resources\SystemCouponsResource\Pages;

use App\Filament\Resources\SystemCouponsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemCoupons extends EditRecord
{
    protected static string $resource = SystemCouponsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
