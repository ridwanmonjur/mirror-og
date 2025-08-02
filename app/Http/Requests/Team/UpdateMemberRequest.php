<?php

namespace App\Http\Requests\Team;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMemberRequest extends FormRequest
{
    protected $teamMember;

    protected $team;

    protected $status;

    protected $isSameActor;

    protected $newToOldRules;

    protected $errorMessages;

    public function authorize()
    {
        return true; // Add your authorization logic here
    }

    public function rules()
    {
        return [
            'status' => 'required|in:accepted,rejected,left,pending',
            'actor' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: accepted, rejected, left, pending',
            'actor.required' => 'Actor is required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->teamMember = TeamMember::find($this->route('id'));

        if ($this->teamMember) {
            $this->team = Team::find($this->teamMember->team_id);
            $this->isSameActor = $this->actor == $this->teamMember->actor;
            $this->newToOldRules = [
                'left' => true,
                'accepted' => [
                    'pending' => ! $this->isSameActor,
                    'rejected' => $this->isSameActor,
                    'left' => $this->isSameActor,
                ],
                'rejected' => [
                    'pending' => ! $this->isSameActor,
                    'rejected' => $this->isSameActor,
                    'accepted' => $this->isSameActor,
                ],
            ];
            $this->errorMessages = [
                'accepted' => [
                    'pending' => $this->isSameActor ? 'You cannot accept your own pending request' : '',
                    'rejected' => ! $this->isSameActor ? 'Only the original requester can accept after rejection' : '',
                    'left' => ! $this->isSameActor ? 'Only the original member can accept after leaving' : '',
                    'accepted' => 'Request is already accepted',
                ],
                'rejected' => [
                    'pending' => $this->isSameActor ? 'You cannot reject your own pending request' : '',
                    'rejected' => ! $this->isSameActor ? 'Only the original requester can modify a rejected request' : '',
                    'accepted' => ! $this->isSameActor ? 'Only the accepted member can reject their request' : '',
                ],
            ];
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateAll($validator);
        });
    }

    protected function validateAll($validator)
    {
        if (! $this->teamMember) {
            $validator->errors()->add('team_member', 'Team member not found');

            return;
        }

        if ($this->team && $this->team->creator_id == $this->teamMember->user_id && $this->status != 'accepted') {
            $validator->errors()->add('team_creator', "Can't modify creator of the team");

            return;
        }

        if ($this->status == 'accepted') {

            if ($this->teamMember->countTeamMembers() >= 10) {
                $validator->errors()->add('team_limit', 'Too many members are already in the team');

                return;
            }

            if ($this->team->status == 'private') {
                $validator->errors()->add('team_private', "It's a private team with only invites allowing new members");

                return;
            } elseif ($this->team->status !== 'public') {
                $this->status = 'pending';
            }

            $user_id = $this->attributes->get('user')->id;

            $count = Team::whereHas('members', function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 'accepted');
            })->count('id');

            if ($count > 5) {
                $validator->errors()->add('team_limit', "You already members of $count teams");

                return;
            }

            $user = $this->attributes->get('user');
            if ($user->participant && $user->participant->team_left_at) {
                $timeSinceLeft = now()->diffInHours($user->participant->team_left_at);
                if ($timeSinceLeft < 24) {
                    $hoursRemaining = 24 - $timeSinceLeft;
                    $validator->errors()->add('team_grace_period', "You must wait {$hoursRemaining} more hours before joining a new team after leaving your previous team.");

                    return;
                }
            }
        }

        if (! $this->status) {
            return;
        }

        $isPermitted = $this->newToOldRules[$this->status] ?? false;

        if (is_array($isPermitted)) {
            $isPermitted = $isPermitted[$this->teamMember->status] ?? false;
        }

        if (! $isPermitted) {
            $message = 'This request is not allowed. ';
            if (isset($this->errorMessages[$this->status][$this->teamMember->status])) {
                $message .= $this->errorMessages[$this->status][$this->teamMember->status];
            }

            $validator->errors()->add('status_transition', trim($message));
        }
    }

    public function getTeamMember()
    {
        return $this->teamMember;
    }

    public function getTeam()
    {
        return $this->team;
    }

    public function isSameActor()
    {
        return $this->isSameActor;
    }

    public function getValidationDetails()
    {
        return [
            'requested_status' => $this->status,
            'current_status' => $this->teamMember ? $this->teamMember->status : null,
            'is_same_actor' => $this->isSameActor,
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $firstError = $errors->first();

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $firstError,
                'errors' => $errors,
            ], 422)
        );

    }
}
