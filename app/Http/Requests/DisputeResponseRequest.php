<?php

namespace App\Http\Requests;

use App\Models\Dispute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DisputeResponseRequest extends FormRequest
{
    /**
     * The validated dispute instance.
     */
    private ?Dispute $validatedDispute = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                'in:respond'
            ],
            'dispute_id' => [
                'required',
                'integer',
                'exists:disputes,id',
                function ($attribute, $value, $fail) {
                    $dispute = Dispute::find($value);

                    if (!$dispute) {
                        $fail('Dispute not found');
                        return;
                    }

                    if ($dispute->response_userId) {
                        $fail('This dispute has already been responded to');
                        return;
                    }

                    $this->validatedDispute = $dispute;
                }
            ],
            'response_teamId' => [
                'required',
                'exists:teams,id',
            ],
            'response_teamNumber' => [
                'required',
            ],
            'response_explanation' => [
                'required',
            ],
            'media' => [
                'nullable',
                'array',
                'max:5' 
            ],
            'media.*' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,mp4,mov',
                'max:20480' // 20MB
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'The action field is required',
            'action.in' => 'Invalid action specified',
            'dispute_id.required' => 'The dispute ID is required',
            'dispute_id.exists' => 'The specified dispute does not exist',
            'response_teamId.required' => 'The response team ID is required',
            'response_teamId.exists' => 'The specified team does not exist',
            'response_teamNumber.required' => 'The team number is required',
            'response_explanation.required' => 'An explanation is required',
            'response_explanation.min' => 'The explanation must be at least :min characters',
            'response_explanation.max' => 'The explanation cannot exceed :max characters',
            'media.max' => 'You cannot upload more than :max files',
            'media.*.mimes' => 'Only jpeg, png, jpg, mp4, and mov files are allowed',
            'media.*.max' => 'Each file must not exceed 20MB'
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

    /**
     * Get the validated dispute instance.
     */
    public function getDispute(): Dispute
    {
        return $this->validatedDispute;
    }
  
}