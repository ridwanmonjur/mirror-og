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
            'id' => 'required',
            'bio' => 'nullable|string',
            'age' => 'nullable|integer|min:0|max:150',
            'nickname' => 'nullable|string|max:255',
            'region' => 'nullable|string',
            'domain' => 'nullable|url',
        ];
    }
}
