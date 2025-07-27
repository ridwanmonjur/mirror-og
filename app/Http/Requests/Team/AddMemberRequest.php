<?php

namespace App\Http\Requests\Team;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddMemberRequest extends FormRequest
{
    protected $team;
    protected $user;
    protected $existingMember;

    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [];
    }

    protected function prepareForValidation()
    {
        $this->team = Team::find($this->route('id'));
        $this->user = $this->attributes->get('user');
        
        if ($this->team && $this->user) {
            $this->existingMember = TeamMember::where([
                'user_id' => $this->user->id,
                'team_id' => $this->team->id
            ])->first();
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
        if (!$this->team) {
            $validator->errors()->add('team', 'Team not found');
            return;
        }

        if (!$this->user) {
            $validator->errors()->add('user', 'User not found');
            return;
        }

        if ($this->team->status === 'private') {
            $validator->errors()->add('team_private', "It's a private team with only invites allowing new members");
            return;
        }

        if ($this->existingMember) {
            $validator->errors()->add('duplicate_request', 'You have requested before!');
            return;
        }

        $user_id = $this->user->id;
        $count = Team::whereHas('members', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->where('status', 'accepted');
        })->count('id');

        if ($count >= 5) {
            $validator->errors()->add('team_limit', "You are already a member of $count teams!");
            return;
        }


        $tempMember = new TeamMember([
            'user_id' => $this->user->id,
            'team_id' => $this->team->id,
            'status' => $this->getStatusByTeamType(),
            'actor' => 'user',
        ]);

        if ($tempMember->countTeamMembers() >= 10) {
            $validator->errors()->add('team_limit', 'Too many members in the team');
            return;
        }

        if ($this->user->participant && $this->user->participant->team_left_at) {
            $timeSinceLeft = now()->diffInHours($this->user->participant->team_left_at);
            if ($timeSinceLeft < 24) {
                $hoursRemaining = 24 - $timeSinceLeft;
                $validator->errors()->add('team_grace_period', "You must wait {$hoursRemaining} more hours before joining a new team after leaving your previous team.");
                return;
            }
        }
    }

    public function getTeam()
    {
        return $this->team;
    }

   

    public function getStatusByTeamType()
    {
        if (!$this->team) {
            return 'pending'; 
        }

        return $this->team->status == 'open' ? 'accepted' : 'pending';
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $firstError = $errors->first();

        if ($this->expectsJson()) {
            // For JSON requests, return JSON response
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => $firstError,
                    'errors' => $errors
                ], 422)
            );
        }

        throw new HttpResponseException(
            redirect()->back()->with('errorJoin', $firstError)
        );
    }
}
