<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class TeamSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Anyone can search teams
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'q' => 'nullable|string|max:255',
            'cursor' => 'nullable|string',
            'per_page' => 'nullable|integer|min:5|max:100',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'q' => 'search query',
            'per_page' => 'results per page',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Trim search query
        if ($this->has('q')) {
            $this->merge([
                'q' => trim($this->q),
            ]);
        }
    }

    /**
     * Get validated and prepared search parameters with defaults.
     *
     * @return array
     */
    public function searchParams()
    {
        $validated = $this->validated();
        
        return [
            'query' => $validated['q'] ?? '',
            'cursor' => $validated['cursor'] ?? null,
            'perPage' => $validated['per_page'] ?? 15,
        ];
    }
}