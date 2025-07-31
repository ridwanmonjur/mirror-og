<?php

namespace App\Http\Requests\Match;

use App\Models\BracketDeadline;
use App\Models\EventDetail;
use App\Models\Brackets;
use App\Models\TeamMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ValidateBracketUpdateRequest extends FormRequest
{
    protected $failureMessage = '';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $now = now();

        $match = Brackets::where([
            'team1_id' => $this->team1_id,
            'team1_position' => $this->team1_position,
            'team2_id' => $this->team2_id,
            'team2_position' => $this->team2_position,
            'event_details_id' => $this->id
        ])->first();

        // dd($match);

        if (!$match) {
            $this->failureMessage = 'The match is not found in tournament bracket! Are you editing in the right place?';
            return false;
        }


       
       
        if ($user->role == "ORGANIZER") {
            $event = EventDetail::where('id', $this->id)
                ->where('user_id', $user->id)
                ->select(['id', 'user_id'])
                ->first();

            if (!$event || $event->user_id != $user->id) {
                $this->failureMessage = 'This is not your event!';
                return false;
            }
        } elseif ($user->role == "PARTICIPANT") {
            if ($this->willCheckDeadline) {
                $bracketDeadline = BracketDeadline::where('stage', $match->stage_name)
                    ->where('inner_stage', $match->inner_stage_name)
                    ->where('event_details_id', $this->id)
                    ->whereDate('start_date', '<=', $now)
                    ->whereDate('end_date', '>=', $now)
                    ->first();
    
                if (!$bracketDeadline) {
                    $this->failureMessage = 'Match is not within reporting timeframe!';
                    return false;
                }
            }

            if (!$this->my_team_id) {
                $this->failureMessage = 'No valid team ID provided';
                return false;
            }

            $teamMember = TeamMember::where([
                'user_id' => $user->id, 
                'team_id' => $this->my_team_id,
                'status' => 'accepted'
            ])->first(); 
            
            if (!$teamMember) {
                $this->failureMessage = 'You are not a member of this team';
                return false;
            }
            
        } else {
            $this->failureMessage = 'No valid user role';
            return false;
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
            'team1_id' => 'nullable',
            'team1_position' => 'nullable',
            'team2_id' => 'nullable',
            'team2_position' => 'nullable',
            'my_team_id' => 'nullable',
            'willCheckDeadline' => 'nullable',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'message' => $this->failureMessage ?: 'You are not authorized to perform this action',
                'success' => false
            ], JsonResponse::HTTP_FORBIDDEN)
        );
    }
}