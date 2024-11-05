<?php

namespace App\Http\Requests\Match;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class MatchUpsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'id' => 'nullable',
            'event_details_id' => 'required',
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
            'status' => 'nullable|in:upcoming,ongoing,completed',
            'result' => 'nullable|in:draw,win,dispute',
            'winner_next_position' => 'nullable|string',
            'loser_next_position' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        
        $response = new JsonResponse([
            'message' => 'The given data was invalid.',
            'errors' => $this->transformErrors($errors),
            'status_code' => 422
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        throw new HttpResponseException($response);
    }

    /**
     * Transform the error messages into a more useful format.
     *
     * @param  \Illuminate\Support\MessageBag  $errors
     * @return array
     */
    protected function transformErrors($errors)
    {
        $transformed = [];

        foreach ($errors->messages() as $field => $messages) {
            $transformed[] = [
                'field' => $field,
                'messages' => $messages,
            ];
        }

        return $transformed;
    }
}
