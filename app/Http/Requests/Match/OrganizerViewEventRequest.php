<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\EventInvitation;
use App\Models\JoinEvent;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\UnauthorizedException;

class OrganizerViewEventRequest extends FormRequest
{
    protected ?EventDetail $event = null;

    protected ?JoinEvent $existingJoint = null;

    protected ?User $user = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator($validator)
    {
        $eventId = $this->route('event');
        $user = $this->user();
        $this->user = $user;
        $userId = $user?->id;
        try {
            $this->event = EventDetail::findEventWithRelationsAndThrowError(
                $userId,
                $eventId,
                null,
                [],
                ['joinEvents' => function ($q) {
                    $q->where('join_status', 'confirmed');
                }]
            );
            $this->existingJoint = null;
        } catch (Exception $e) {
            $validator->errors()->add('event', "Event not found by ID: {$eventId}");

            return;
        }

        return true;
    }

    /**
     * Get the validated event.
     */
    public function getEvent(): ?EventDetail
    {
        return $this->event;
    }

    public function getJoinEvent(): ?JoinEvent
    {
        return $this->existingJoint;
    }

    public function getStoredUser(): ?User
    {
        return $this->user;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $error = $validator->errors()->first();

        throw new \Illuminate\Validation\ValidationException($validator, response()->view('Participant.Error', [
            'error' => $error,
        ]));
    }
}
