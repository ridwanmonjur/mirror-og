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
            'add.user1_id' => 'required|exists:users,id',
            'add.status' => 'nullable|in:pending,accepted,rejected,left',
            'update.id' => 'required|exists:friends,id',
            'update.status' => 'required|in:pending,accepted,rejected,left',
            'delete.id' => 'required|exists:friends,id',
        ];
    }

    public function messages()
    {
        return [
            'add.user1_id.required' => 'The user id, :value is required for adding a friend.',
            'add.user1_id.exists' => 'The selected user id, :value is invalid.',
            'add.status.in' => 'The selected status, :value is invalid for adding a friend.',
            'update.id.required' => 'The id field, :value is required for updating a friend.',
            'update.id.exists' => 'The selected id, :value is invalid for updating a friend.',
            'update.status.required' => 'The status field, :value is required for updating a friend.',
            'update.status.in' => 'The selected status, :value is invalid for updating a friend.',
            'delete.id.required' => 'The id field, :value is required for deleting a friend.',
            'delete.id.exists' => 'The selected id, :value is invalid for deleting a friend.',
        ];
    }
}
