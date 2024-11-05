<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LikeRequest extends FormRequest
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
    public function rules()
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'event_id' => 'nullable|exists:event_details,id',
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'The selected user id, :id is invalid.',
            'event_id.exists' => 'The selected event id, :id is invalid.',
        ];
    }
}
