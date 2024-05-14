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
            'address.addressLine2' => 'required|string',
            'address.city' => 'required|string',
            'address.country' => 'required|string',
            'address.user_id' => 'required',
            'userProfile.name' => 'required|string',
            'userProfile.id' => 'required',
            'userProfile.mobile_no' => 'required|string',
            'organizer.id' => 'nullable',
            'organizer.industry' => 'required|string',
            'organizer.type' => 'required|string',
            'organizer.companyName' => 'required|string|max:255', 
            'organizer.companyDescription' => 'required|string|max:1000', 
            'organizer.website_link' => 'nullable|url',
            'organizer.instagram_link' => 'nullable|url',
            'organizer.facebook_link' => 'nullable|url',
            'organizer.twitter_link' => 'nullable|url',
        ];
    }


    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'address.addressLine1.required' => 'Address Line 1 is required.',
            'address.addressLine1.string' => 'Address Line 1 must be a string.',
            'address.addressLine2.required' => 'Address Line 2 is required.',
            'address.addressLine2.string' => 'Address Line 2 must be a string.',
            'address.city.required' => 'City is required.',
            'address.city.string' => 'City must be a string.',
            'address.country.required' => 'Country is required.',
            'address.country.string' => 'Country must be a string.',
            'address.user_id.required' => 'User ID is required.',
            'userProfile.name.required' => 'Name is required.',
            'userProfile.name.string' => 'Name must be a string.',
            'userProfile.id.required' => 'User Profile ID is required.',
            'userProfile.mobile_no.required' => 'Mobile number is required.',
            'userProfile.mobile_no.string' => 'Mobile number must be a string.',
            'organizer.industry.required' => 'Industry is required.',
            'organizer.industry.string' => 'Industry must be a string.',
            'organizer.type.required' => 'Type is required.',
            'organizer.type.string' => 'Type must be a string.',
            'organizer.companyName.required' => 'Company Name is required.',
            'organizer.companyName.string' => 'Company Name must be a string.',
            'organizer.companyName.max' => 'Company Name must not exceed 255 characters.',
            'organizer.companyDescription.required' => 'Company Description is required.',
            'organizer.companyDescription.string' => 'Company Description must be a string.',
            'organizer.companyDescription.max' => 'Company Description must not exceed 1000 characters.',
            'organizer.website_link.url' => 'Website Link must be a valid URL.',
            'organizer.instagram_link.url' => 'Instagram Link must be a valid URL.',
            'organizer.facebook_link.url' => 'Facebook Link must be a valid URL.',
            'organizer.twitter_link.url' => 'Twitter Link must be a valid URL.',
        ];
    }

    protected function prepareForValidation()
    {
        $attributes = $this->request->all();
        $links = ['website_link', 'instagram_link', 'twitter_link', 'facebook_link'];

        foreach ($links as $link) {
            if (isset($attributes[$link])) {
                if (substr($attributes[$link], -1) === '/') {
                    $attributes[$link] = rtrim($attributes[$link], '/');
                }
            }
        }

        $this->request->replace($attributes);
    }
}
