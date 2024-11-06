<?php

namespace App\Http\Requests\User;

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
            'addUserId' => 'nullable',
            'updateUserId' => 'nullable',
            'updateStatus' => 'nullable|in:pending,accepted,rejected,left',
            'deleteUserId' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'addUserId.nullable' => 'The selected user id, :value is invalid.',
            'updateUserId.nullable' => 'The selected id, :value is invalid for updating a friend.',
            'update.status.in' => 'The selected status, :value is invalid for updating a friend.',
            'deleteUserId.nullable' => 'The selected id, :value is invalid for deleting a friend.',
        ];
    }
}
