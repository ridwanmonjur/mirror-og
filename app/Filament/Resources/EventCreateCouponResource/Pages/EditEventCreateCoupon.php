<?php

namespace App\Filament\Resources\EventCreateCouponResource\Pages;

use App\Filament\Resources\EventCreateCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventCreateCoupon extends EditRecord
{
    protected static string $resource = EventCreateCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
