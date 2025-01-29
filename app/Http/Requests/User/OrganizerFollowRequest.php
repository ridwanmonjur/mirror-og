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
        $this->organizer = User::where('id', $this->organizer_id)
            ->where('role', 'ORGANIZER')
            ->select(['id', 'role', 'name'])
            ->firstOrFail();
            
        $user = $this->attributes->get('user');
        return $user->role === 'PARTICIPANT' 
            && $this->organizer_id != $user->id;
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
