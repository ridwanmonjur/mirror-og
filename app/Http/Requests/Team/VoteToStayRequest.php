<?php

namespace App\Http\Requests\Team;

use App\Models\RosterMember;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class VoteToStayRequest extends FormRequest
{
    public RosterMember $rosterMember ;
    /**
     * Determine if the user is authorized to make this request.
     */

     public function authorize(): bool
     {
         return true;
     }
 
     public function withValidator($validator)
     {
         $validator->after(function ($validator) {
  
            $rosterMember = RosterMember::find($this->roster_id);

            if (!$rosterMember) {
                $validator->errors()->add('event',  'Roster member not found');
            }

            $this->rosterMember = $rosterMember;

            if ($rosterMember->user_id !== $this->attributes->get('user')?->id) {
                $validator->errors()->add('event',  'Unauthorized to vote for this roster member');
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
            'roster_id' => [
                'required',
            ],
            'vote_to_quit' => [
                'required',
                'boolean'
            ]
    
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        
        $error = $validator->errors()->first();
    
        throw new ValidationException($validator, response()->json( [
            'message' => $error,
            'success'=> false
        ], 422));
    }
}
