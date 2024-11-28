<?php

namespace App\Http\Requests\Team;

use App\Models\RosterMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class VoteToStayRequest extends FormRequest
{
    public RosterMember $rosterMember ;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $rosterMember = RosterMember::find($this->roster_id);

        if (!$rosterMember) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Roster member not found',
                ], Response::HTTP_NOT_FOUND)
            );
        }

        $this->rosterMember = $rosterMember;

        if ($rosterMember->user_id !== $this->attributes->get('user')?->id) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to vote for this roster member',
                ], Response::HTTP_FORBIDDEN)
            );
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'roster_id' => [
                'required',
            ],
            'vote_to_quit' => [
                'required',
                'boolean'
            ]
    
        ];
    }
}
