<?php

namespace App\Http\Requests\Team;

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
            'teamName' => 'required|string|max:25',
            'teamDescription' => 'sometimes|max:150',
            'country' => 'nullable',
            'country_name' => 'nullable|string',
            'country_flag' => 'nullable|string',
            'file' => 'sometimes|array|nullable',
            'file.filename' => 'string',
            'file.type' => 'string',
            'file.size' => 'numeric|max:3',
            'file.content' => 'string',
            'all_categories' => 'string',
            'default_category_id' => 'required',
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
            'country.required' => 'The country field is required.',
            'country.string' => 'The country must be a string.',
            'teamName.max' => 'Team name cannot exceed 25 characters',
            'teamDescription.required' => 'Please add a description for your team',
            'teamDescription.max' => 'Team description must be less than 150 characters',
            'file.size' => 'The file is too large, over 3 MB in size'

        ];
    }
}
