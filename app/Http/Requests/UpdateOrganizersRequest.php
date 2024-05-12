<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizersRequest extends FormRequest
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
            'address.id' => 'nullable',
            'address.addressLine1' => 'required|string',
            'address.addressLine2' => 'nullable|string',
            'address.city' => 'required|string',
            'address.country' => 'required|string',
            'address.user_id' => 'required',
            'userProfile.name' => 'required|string',
            'userProfile.id' => 'required',
            'userProfile.mobile_no' => 'nullable|string',
            'organizer.id' => 'nullable',
            'organizer.industry' => 'nullable|string',
            'organizer.type' => 'nullable|string',
            'organizer.companyName' => 'nullable|string|max:255', 
            'organizer.companyDescription' => 'nullable|string|max:1000', // Adjust the maximum length as needed
            'organizer.website_link' => 'nullable|url',
            'organizer.instagram_link' => 'nullable|url',
            'organizer.facebook_link' => 'nullable|url',
            'organizer.twitter_link' => 'nullable|url',
        ];
    }
}
