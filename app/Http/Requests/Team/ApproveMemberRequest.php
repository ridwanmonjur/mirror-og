<?php

namespace App\Http\Requests\Team;

use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class ApproveMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public string|int $member_id ;

    public function authorize(): bool
    {
        $userId = $this->user_id;
        $teamMember = TeamMember::where([
            'user_id' => $userId,
            'team_id' => $this->team_id
        ])->first();

        if (!$teamMember) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Team member not found',
                ], Response::HTTP_NOT_FOUND)
            );
        }

        if ($teamMember->status!="accepted") {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Member is not accepted',
                ], Response::HTTP_FORBIDDEN)
            );
        }

        $this->member_id = $teamMember->id;

        return true;
       
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
                'exists:users,id'
            ],
            'join_events_id' => [
                'required',
                'integer',
                'exists:join_events,id'
            ],
            'team_id' => [
                'required',
                'integer',
                'exists:teams,id'
            ]
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
            'team_id.exists' => 'Selected team does not exist'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}