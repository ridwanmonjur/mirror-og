<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class OrganizerFollowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public ?User $organizer = null;

    public function authorize(): bool
    {
        return true;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->organizer = User::where('id', $this->organizer_id)
                ->where('role', 'ORGANIZER')
                ->select(['id', 'role', 'name', 'userBanner'])
                ->first();

            if (! $this->organizer) {
                $validator->errors()->add('event', 'Organizer not found');
            }

            $user = $this->attributes->get('user');
            if (! ($user->role === 'PARTICIPANT'
                && $this->organizer_id != $user->id)) {
                $validator->errors()->add('event', 'Wrong role!');
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organizer_id' => ['required', 'integer'],
        ];
    }
}
