<?php

namespace App\Filament\Resources\WithdrawalPasswordResource\Pages;

use App\Filament\Resources\WithdrawalPasswordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWithdrawal extends CreateRecord
{
    protected static string $resource = WithdrawalPasswordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set requested_at to current time if not provided
        if (! isset($data['requested_at'])) {
            $data['requested_at'] = now();
        }

        return $data;
    }
}
