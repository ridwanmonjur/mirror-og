<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
       throw new HttpResponseException(
           response()->json([
               'success' => false,
               'message' => 'Invalid request format',
               'errors' => $validator->errors(),
           ], 422)
       );
   }
}
