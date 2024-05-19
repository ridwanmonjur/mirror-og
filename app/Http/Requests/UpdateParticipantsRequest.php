<?php

namespace App\Http\Requests;

use Carbon\Carbon;
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
            'id' => 'required',
            'bio' => 'nullable|string',
            'age' => 'nullable|numeric',
            'birthday' => 'nullable|date|before_or_equal:' . now()->format('Y-m-d'),
            'nickname' => 'nullable|string|max:255',
            'region' => 'nullable|string',
            'games_data' => 'nullable|string',
            'domain' => ['nullable', 'regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'The ID field is required.',
            'bio.string' => 'The bio must be a string.',
            'birthday.date' => 'The birthday must be a valid date.',
            'birthday.before_or_equal' => 'The birthday must be a date before the current year.',
            'nickname.string' => 'The nickname must be a string.',
            'nickname.max' => 'The nickname may not be greater than 255 characters.',
            'region.string' => 'The region must be a string.',
            'domain.regex' => 'The domain format is invalid. It must be a valid domain name.',
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
