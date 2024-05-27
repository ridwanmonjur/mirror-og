<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FriendRequest extends FormRequest
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
            'addUserId' => 'nullable|exists:users,id',
            'updateUserId' => 'nullable|exists:users,id',
            'updateStatus' => 'nullable|in:pending,accepted,rejected,left',
            'deleteUserId' => 'nullable|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'addUserId.exists' => 'The selected user id, :value is invalid.',
            'updateUserId.exists' => 'The selected id, :value is invalid for updating a friend.',
            'update.status.in' => 'The selected status, :value is invalid for updating a friend.',
            'deleteUserId.exists' => 'The selected id, :value is invalid for deleting a friend.',
        ];
    }
}
