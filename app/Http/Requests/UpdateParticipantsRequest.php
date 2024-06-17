<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParticipantsRequest extends FormRequest
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
            'user.id' => 'required',
            'user.name' => 'required|string',
            'participant.id' => 'required',
            'participant.bio' => 'nullable|string',
            'participant.age' => 'nullable|numeric',
            'participant.birthday' => 'nullable|date|before_or_equal:'.now()->format('Y-m-d'),
            'participant.nickname' => 'nullable|string|max:255',
            'participant.region' => 'nullable',
            'participant.region_name' => 'nullable',
            'participant.region_flag' => 'nullable',
            'participant.isAgeVisible' => 'nullable|boolean',
            'participant.games_data' => 'nullable|string',
            'participant.domain' => ['nullable', 'regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user.id.required' => 'The ID field is required.',
            'user.name.required' => 'The name must be a string.',
            'user.name.string' => 'The name must be a string.',
            'participant.id.required' => 'The ID field is required.',
            'participant.bio.string' => 'The bio must be a string.',
            'participant.birthday.date' => 'The birthday must be a valid date.',
            'participant.birthday.before_or_equal' => 'The birthday must be a date before the current year.',
            'participant.nickname.string' => 'The nickname must be a string.',
            'participant.nickname.max' => 'The nickname may not be greater than 255 characters.',
            'participant.region.array' => 'The region must be a json string.',
            'participant.domain.regex' => 'The domain format is invalid. It must be a valid domain name.',
        ];
    }

    protected function prepareForValidation()
    {
        $attributes = $this->request->all();

        if (isset($attributes['domain']) && filter_var($attributes['domain'], FILTER_VALIDATE_URL) !== false) {
            $parsed = parse_url($attributes['domain']);
            $attributes['domain'] = $parsed['host'];
        }

        $this->request->replace($attributes);
    }
}
