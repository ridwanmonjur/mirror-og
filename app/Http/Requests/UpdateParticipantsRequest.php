<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateParticipantsRequest extends FormRequest
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
            'id' => 'required',
            'bio' => 'required|string',
            'birthday' => 'required|date', 
            'nickname' => 'required|string|max:255',
            'region' => 'required|string',
            'domain' => ['required', 'regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/'],
        ];
    }

    protected function prepareForValidation()
    {
        $attributes = $this->request->all();

        if (isset($attributes['birthday'])) {
            $attributes['birthday'] = Carbon::parse($attributes['birthday']);
            $attributes['age'] = Carbon::now()->diffInYears($attributes['birthday']);
        }

        if (isset($attributes['domain']) && filter_var($attributes['domain'], FILTER_VALIDATE_URL) !== false) {
            $parsed = parse_url($attributes['domain']);
            $attributes['domain'] = $parsed['host'];
        }

        $this->request->replace($attributes);
    }

    
}
