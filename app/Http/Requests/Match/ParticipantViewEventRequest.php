<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\EventInvitation;
use App\Models\JoinEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\UnauthorizedException;

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
        $eventId = $this->route('id');
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

        if (!$this->event) {
            throw new ModelNotFoundException("Event not found by id: {$eventId}");
        }

        $status = $this->event->statusResolved();
        if (in_array($status, ['DRAFT', 'PREVEW', 'PENDING'])) {
            $lowerStatus = strtolower($status);
            throw new ModelNotFoundException("Can't display event: {$eventId} with status: {$lowerStatus}");
        }

        $user = $this->user();
        $this->user = $user;
        $userId = $user?->id;

        // Handle scheduled events
        if ($status === 'SCHEDULED' && $userId) {
            $isOrganizer = $this->event->user_id === $userId;
            if (!$isOrganizer) {
                throw new UnauthorizedException('You cannot view a scheduled event');
            }
        }

        // Handle private events
        if ($this->event->sub_action_private === 'private') {
            if (!$user) {
                throw new UnauthorizedException('Login to access this event.');
            }

            $existingJoint = JoinEvent::getJoinedByTeamsForSameEvent($this->event->id, $userId);
            $this->existingJoint = $existingJoint;
            $isOrganizer = $this->event->user_id === $userId;
            $isInvited = EventInvitation::where([
                'team_id' => $existingJoint?->team_id,
                'event_id' => $this->event->id
            ])->exists();

            if (!($isOrganizer || $isInvited)) {
                throw new UnauthorizedException("This is a private event and you're neither organizer nor a participant of event");
            }
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Add any additional validation rules for query parameters if needed
        ];
    }
}
