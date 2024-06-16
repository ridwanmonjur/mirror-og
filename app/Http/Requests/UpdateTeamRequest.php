<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
            'id' => 'required|numeric',
            'teamName' => 'required|string',
            'teamDescription' => 'required|string',
            'country' => 'nullable',
            'country_name' => 'nullable|string',
            'country_flag' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'The ID field is required.',
            'id.numeric' => 'The ID must be a number.',
            'teamName.required' => 'The team name is required.',
            'teamName.string' => 'The team name must be a string.',
            'teamDescription.required' => 'The team description is required.',
            'teamDescription.string' => 'The team description must be a string.',
            'country.required' => 'The country field is required.',
            'country.string' => 'The country must be a string.',
        ];
    }
}
