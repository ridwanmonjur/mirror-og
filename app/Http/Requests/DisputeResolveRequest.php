<?php

namespace App\Http\Requests;

use App\Models\Dispute;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
class DisputeResolveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization requirements
    }

    private ?Dispute $validatedDispute = null;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'dispute_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $dispute = Dispute::find($value);

                    if (!$dispute) {
                        $fail("Cannot resolve dispute as it doesn't exist");
                    }
                    
                    if (!$dispute->response_userId) {
                        $fail('Cannot resolve dispute before response is submitted');
                    }
                    
                    if ($dispute->resolution_winner) {
                        $fail('This dispute has already been resolved');
                    }

                    $this->validatedDispute = $dispute;
                },
            ],
            'resolution_winner' => [
                'required',
                'string',
            ],
            'resolution_resolved_by' => [
                'required',
            ],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'dispute_id.required' => 'A dispute ID is required',
            'dispute_id.integer' => 'The dispute ID must be a number',
            'dispute_id.exists' => 'The specified dispute does not exist',
            'resolution_winner.required' => 'A resolution winner must be specified',
            'resolution_resolved_by.required' => 'A resolver must be specified',

        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422)
        );
    }

    public function getDispute(): Dispute
    {
        return $this->validatedDispute;
    }

}