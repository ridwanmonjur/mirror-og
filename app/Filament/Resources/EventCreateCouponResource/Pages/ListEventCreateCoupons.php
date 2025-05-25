<?php

namespace App\Filament\Resources\EventCreateCouponResource\Pages;

use App\Filament\Resources\EventCreateCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventCreateCoupons extends ListRecords
{
    protected static string $resource = EventCreateCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
