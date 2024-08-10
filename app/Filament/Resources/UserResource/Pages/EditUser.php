<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        if (! is_null($data['role'])) {
            // @phpstan-ignore-next-line
            $record->role = $data['role'];
        }

        if (! is_null($data['status'])) {
            // @phpstan-ignore-next-line
            $record->status = $data['status'];
        }

        if (! is_null($data['email_verified_at'])) {
            // @phpstan-ignore-next-line
            $record->email_verified_at = $data['email_verified_at'];
        }

        $record->save();

        return $record;
    }
}
