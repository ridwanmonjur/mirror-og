<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisputeCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => 'required',
            'dispute_teamId' => 'required|exists:teams,id',
            'dispute_teamNumber' => 'required|string',
            'dispute_reason' => 'required|string|max:255',
            'dispute_description' => 'nullable|string',
            'media.*' => 'required|file|mimes:jpeg,png,jpg,mp4,mov|max:20480'
        ];
    }
}
