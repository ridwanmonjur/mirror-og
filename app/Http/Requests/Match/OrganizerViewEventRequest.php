<?php

namespace App\Http\Requests\Match;

use App\Models\EventDetail;
use App\Models\EventInvitation;
use App\Models\JoinEvent;
use App\Models\User;
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
        $eventId = $this->route('event');
        $user = $this->user();
        $this->user = $user;
        $userId = $user?->id;

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
