<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class EmailValidationRequest extends FormRequest
{
   public function authorize(): bool
   {
       return true; // Modify based on your authorization logic
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, array<int, string>>
    */
   public function rules(): array
   {
       return [
           'email' => ['required', 'email'],
       ];
   }

   /**
    * Get custom messages for validator errors.
    *
    * @return array<string, string>
    */
   public function messages(): array
   {
       return [
           'email.required' => 'Email is required',
           'email.email' => 'Please provide a valid email address',
       ];
   }

   /**
    * Handle a failed validation attempt.
    */
    protected function failedValidation(Validator $validator)
    {
        
        $error = $validator->errors()->first();
    
        throw new ValidationException($validator, response()->json( [
            'message' => $error,
            'success'=> false
        ], 422));
    }
}
