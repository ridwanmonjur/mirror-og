<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Participant;
use App\Models\Organizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;


class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = static::getModel();
        try {
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

            // Show success notification
            Notification::make()
                ->title('User created successfully')
                ->success()
                ->send();

            return $user;

        } catch (\Illuminate\Database\QueryException $e) {
            // Log the full error for debugging
            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);

            // Handle specific database errors
            if ($e->errorInfo[1] === 1062) { // Duplicate entry error
                Notification::make()
                    ->title('Email Already Exists')
                    ->body('This email address is already registered. Please use a different email.')
                    ->danger()
                    ->send();
                
                $this->halt(); // Prevents the form from closing
            }

            // Generic database error
            Notification::make()
                ->title('Creation Failed')
                ->body('Unable to create user. Please try again or contact support if the problem persists.')
                ->danger()
                ->send();
            
            $this->halt();
            return $user;

        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Unexpected error during user creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Unexpected Error')
                ->body('An unexpected error occurred. Please try again.')
                ->danger()
                ->send();
            
            $this->halt();
            return $user;
        }
    }
}