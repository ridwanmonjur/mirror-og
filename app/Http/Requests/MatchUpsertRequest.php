<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatchUpsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): array
    {
        return [
            'id' => 'sometimes',
            'event_details_id' => 'required|exists:event_details,id',
            'match_type' => 'required|string',
            'stage_name' => 'nullable|string',
            'inner_stage_name' => 'nullable|string',
            'order' => 'nullable|integer',
            'team1_id' => 'required',
            'team2_id' => 'required',
            'team1_score' => 'required|integer|min:0',
            'team2_score' => 'required|integer|min:0',
            'team1_position' => 'nullable|string',
            'team2_position' => 'nullable|string',
            'winner_id' => 'nullable|exists:teams,id',
            'status' => 'required|in:upcoming,ongoing,completed',
            'result' => 'required|in:draw,win,dispute',
            'winner_next_position' => 'nullable|string',
            'loser_next_position' => 'nullable|string',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
