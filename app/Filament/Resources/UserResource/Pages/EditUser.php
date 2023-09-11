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

        if (!is_null( $data['role'] )) {
            $record->role= $data['role'];
        }

        if (!is_null( $data['status'] )) {
            $record->status= $data['status'];
        }

        if (!is_null( $data['email_verified_at'] )) {
            $record->email_verified_at= $data['email_verified_at'];
        }

        $record->save();

        return $record;
    }
}
