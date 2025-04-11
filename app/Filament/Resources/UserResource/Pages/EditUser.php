<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Participant;
use App\Models\Organizer;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get the user
        $user = $this->record;
        
        // Add participant data if available
        if ($user->role === 'PARTICIPANT' && $user->participant) {
            $data['participant'] = $user->participant->toArray();
        }
        
        // Add organizer data if available
        if ($user->role === 'ORGANIZER' && $user->organizer) {
            $data['organizer'] = $user->organizer->toArray();
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update user data
        $record->update($data);

        // Update or create participant data
        if ($record->role === 'PARTICIPANT' && isset($data['participant'])) {
            $participant = Participant::firstOrNew(['user_id' => $record->id]);
            $participant->fill($data['participant']);
            $participant->save();
        }

        // Update or create organizer data
        if ($record->role === 'ORGANIZER' && isset($data['organizer'])) {
            $organizer = Organizer::firstOrNew(['user_id' => $record->id]);
            $organizer->fill($data['organizer']);
            $organizer->save();
        }

        return $record;
    }
}