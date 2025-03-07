<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\EventInvitation;
use App\Models\JoinEvent;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

class ParticipantViewEventRequest extends FormRequest
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
        $validator->after(function ($validator) {
            $eventId = $this->route('id');
            try {
                $this->event = EventDetail::findEventWithRelationsAndThrowError(
                    null,
                    $eventId,
                    null,
                    ['game', 'type', 'joinEvents' => function ($q) {
                        $q->where('join_status', 'confirmed')->with('team');
                    }],
                    ['joinEvents' => function ($q) {
                        $q->where('join_status', 'confirmed');
                    }]
                );
            } catch (Exception $e) {
                $validator->errors()->add('event', "Event not found by ID: {$eventId}");
                return;
            }
    
    
            $status = $this->event->statusResolved();
            if (in_array($status, ['DRAFT', 'PREVIEW', 'PENDING'])) {
                $lowerStatus = strtolower($status);
                $validator->errors()->add('status', "Can't display {$lowerStatus} event with ID: {$eventId} to public view.");
                return;
            }
    
            $user = $this->user();
            $this->user = $user;
            $userId = $user?->id;
    
            // Handle scheduled events
            if ($status === 'SCHEDULED' && $userId) {
                $isOrganizer = $this->event->user_id === $userId;
                if (!$isOrganizer) {
                    $validator->errors()->add('access', 'You cannot view a scheduled event');
                    return;
                }
            }
    
            $existingJoint = JoinEvent::getJoinedByTeamsForSameEvent($this->event->id, $userId);
            $this->existingJoint = $existingJoint;
    
            // Handle private events
            if ($this->event->sub_action_private === 'private') {
                if (!$user) {
                    $validator->errors()->add('authentication', 'Login to access this event.');
                    return;
                }
    
                $isOrganizer = $this->event->user_id === $userId;
                $isInvited = EventInvitation::where([
                    'team_id' => $existingJoint?->team_id,
                    'event_id' => $this->event->id
                ])->exists();
    
                if (!($isOrganizer || $isInvited)) {
                    $validator->errors()->add('permission', "This is a private event and you're neither organizer nor a participant of event");
                    return;
                }
            }
        });
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
    
        throw new \Illuminate\Validation\ValidationException($validator, response()->view('Participant.EventNotFound', [
            'error' => $error
        ]));
    }

    /**
     * Get the validation rules that apply to the request.
     */
   
}
