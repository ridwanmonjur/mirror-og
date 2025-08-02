<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SavePaymentMethodRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|min:4',
            'account_holder_name' => 'required|string|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bank_name.required' => 'Please select a bank',
            'account_number.required' => 'Account number is required',
            'account_holder_name.required' => 'Account holder name is required',
            'account_holder_name.max' => 'Account holder name cannot exceed 100 characters',
        ];
    }
}
