<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class BannerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add authorization logic here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'backgroundBanner' => 'nullable',
            'teamId' => 'nullable|exists:teams,id',
            'backgroundGradient' => 'nullable|string',
            'backgroundColor' => 'nullable|string',
            'fontColor' => 'nullable|string',
            'frameColor' => 'nullable|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'backgroundBanner' => 'Background Banner',
            'teamId' => 'Team',
            'backgroundGradient' => 'Background Gradient',
            'backgroundColor' => 'Background Color',
            'fontColor' => 'Font Color',
            'frameColor' => 'Frame Color',
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
            'teamId.exists' => 'The selected team is invalid.',
        ];
    }
}
