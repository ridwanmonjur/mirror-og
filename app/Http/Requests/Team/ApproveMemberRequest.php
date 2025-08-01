<?php

namespace App\Http\Requests\Team;

use App\Models\JoinEvent;
use App\Models\TeamMember;
use App\Models\RosterMember;
use App\Models\EventCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

class ApproveMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public string|int $member_id;

    public function authorize(): bool
    {
        return true;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $userId = $this->user_id;
            $joinEventsId = $this->join_events_id;

            $joinEvent = JoinEvent::with('eventDetails.game')->find($joinEventsId);
            if (! $joinEvent) {
                $validator->errors()->add('event', 'Join event not found');

                return;
            }

            $eventId = $joinEvent->event_details_id;
            $eventCategoryId = $joinEvent->eventDetails->event_category_id;

            if (JoinEvent::isPartOfRoster($eventId, $userId)) {
                $validator->errors()->add('event', 'This user has already joined this event with the roster of another team!');
            }

            $teamMember = TeamMember::where([
                'user_id' => $userId,
                'team_id' => $this->team_id,
            ])->first();

            if (! $teamMember) {
                $validator->errors()->add('event', 'Team member not found');

                return;
            }

            if ($teamMember->status != 'accepted') {
                $validator->errors()->add('event', 'Member is not accepted');

                return;
            }

            $currentRosterCount = RosterMember::where('join_events_id', $joinEventsId)->count();
            $eventCategory = EventCategory::find($eventCategoryId);
            if (! $eventCategory) {
                $validator->errors()->add('event', 'Event category not found');

                return;
            }

            if ($currentRosterCount >= $eventCategory->player_per_team) {
                $validator->errors()->add('event', "Team roster is full. Maximum {$eventCategory->player_per_team} players allowed for this game category.");

                return;
            }

            $this->member_id = $teamMember->id;

        });
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'join_events_id' => function ($attribute, $value, $fail) {
                $joinEvent = JoinEvent::find($value);
                if (! $joinEvent) {
                    $fail('The selected join event is missing.');
                }

                if ($joinEvent && $joinEvent->join_status != 'pending') {
                    $fail('The selected join event is now locked.');
                }
            },
            'team_id' => [
                'required',
                'integer',
                'exists:teams,id',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'Selected user does not exist',
            'join_events_id.required' => 'Event ID is required',
            'join_events_id.exists' => 'Selected event does not exist',
            'team_id.required' => 'Team ID is required',
            'team_id.exists' => 'Selected team does not exist',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {

        $error = $validator->errors()->first();

        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'message' => $error,
            'success'=> false,
        ], 422));
    }
}
