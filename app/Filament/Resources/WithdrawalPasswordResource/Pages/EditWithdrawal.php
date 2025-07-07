<?php

namespace App\Filament\Resources\WithdrawalPasswordResource\Pages;

use App\Filament\Resources\WithdrawalPasswordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawal extends EditRecord
{
    protected static string $resource = WithdrawalPasswordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}