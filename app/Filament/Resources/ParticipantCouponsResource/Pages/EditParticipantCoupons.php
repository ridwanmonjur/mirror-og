<?php

namespace App\Filament\Resources\ParticipantCouponsResource\Pages;

use App\Filament\Resources\ParticipantCouponsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParticipantCoupons extends EditRecord
{
    protected static string $resource = ParticipantCouponsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
