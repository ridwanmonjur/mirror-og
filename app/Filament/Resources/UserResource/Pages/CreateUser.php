<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Participant;
use App\Models\Organizer;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create the user first
        $user = static::getModel()::create($data);

        // Based on role, create related models
        if ($user->role === 'PARTICIPANT' && isset($data['participant'])) {
            $participantData = $data['participant'];
            $participantData['user_id'] = $user->id;
            Participant::create($participantData);
        } elseif ($user->role === 'ORGANIZER' && isset($data['organizer'])) {
            $organizerData = $data['organizer'];
            $organizerData['user_id'] = $user->id;
            Organizer::create($organizerData);
        }

        return $user;
    }
}