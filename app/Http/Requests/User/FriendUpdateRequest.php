<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Friend;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class FriendUpdateRequest extends FormRequest
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
            'deleteUserId' => 'sometimes|exists:users,id',
            'addUserId' => 'sometimes|exists:users,id',
            'updateUserId' => 'sometimes|exists:users,id',
            'updateStatus' => 'sometimes|in:left,accepted,rejected,pending'
         ];
     }

    public function withValidator($validator)
    {
       
            $validator->after(function ($validator) {
            $user = $this->attributes->get('user');

            if (isset($this->updateStatus) && isset($this->updateUserId)) {
                $friend = Friend::checkFriendship($this->updateUserId, $user->id);
                $status = $this->updateStatus;
                
                $isPermitted = $status === 'left';
                if (!$isPermitted && in_array($status, ['accepted', 'rejected'])) {
                    $isPermitted = ($friend->status === 'pending' && $user->id !== $friend->actor_id) ||
                        ($friend->status === 'left' && $user->id === $friend->actor_id) ||
                        ($friend->status === 'rejected' && $user->id === $friend->actor_id);
                }

                if (!$isPermitted) {
                    $validator->errors()->add(
                        'errorMessage', 
                        "This request is not allowed."
                    );
                }
            }
        });

    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Invalid request format',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        $firstError = $validator->errors()->first();
        session()->flash('errorMessage', $firstError);
        
        parent::failedValidation($validator);
    }
}
